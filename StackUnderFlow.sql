DROP TABLE IF EXISTS POST_MESSAGES;
DROP TABLE IF EXISTS MESSAGES;
DROP TABLE IF EXISTS FOLLOWS;
DROP TABLE IF EXISTS LIKES;
DROP TABLE IF EXISTS POSTS;
DROP TABLE IF EXISTS USERS;
DROP TABLE IF EXISTS THEMES;
DROP TABLE IF EXISTS MOTS_INTERDITS;

CREATE TABLE MOTS_INTERDITS (
    idMotInterdit INT AUTO_INCREMENT,
    nomMotInterdit VARCHAR(50),
    CONSTRAINT Pk_mots_interdit PRIMARY KEY (idMotInterdit)
);

CREATE TABLE THEMES (
    idTheme INT AUTO_INCREMENT,
    nomTheme VARCHAR(32),
    CONSTRAINT Pk_themes PRIMARY KEY (idTheme)
);

CREATE TABLE USERS (
    idUser INT AUTO_INCREMENT,
    pseudo VARCHAR(32),
    mail VARCHAR(255),
    pwd VARCHAR(255),
    pdp VARCHAR(255),
    bio VARCHAR(255),
    statut VARCHAR(20),
    nbAbonnee INT,
    CONSTRAINT Pk_users PRIMARY KEY (idUser)
);

CREATE TABLE POSTS (
    idPost INT AUTO_INCREMENT,
    title VARCHAR(255),
    idTheme INT,
    author INT,
    imagePath VARCHAR(255) NULL,
    CONSTRAINT Pk_posts PRIMARY KEY (idPost),
    CONSTRAINT Fk_posts_author FOREIGN KEY (author) REFERENCES USERS(idUser),
    CONSTRAINT Fk_posts_theme FOREIGN KEY (idTheme) REFERENCES THEMES(idTheme)
);

CREATE TABLE LIKES (
    idUser INT,
    idMessage INT,
    CONSTRAINT Pk_likes PRIMARY KEY (idUser, idMessage),
    CONSTRAINT Fk_likes_user FOREIGN KEY (idUser) REFERENCES USERS(idUser),
    CONSTRAINT Fk_likes_message FOREIGN KEY (idMessage) REFERENCES MESSAGES(idMessage)
);

CREATE TABLE FOLLOWS (
    idUser1 INT,
    idUser2 INT,
    CONSTRAINT Pk_follows PRIMARY KEY (idUser1, idUser2),
    CONSTRAINT Fk_follows_user1 FOREIGN KEY (idUser1) REFERENCES USERS(idUser),
    CONSTRAINT Fk_follows_user2 FOREIGN KEY (idUser2) REFERENCES USERS(idUser)
);

CREATE TABLE MESSAGES (
    idMessage INT AUTO_INCREMENT,
    textMessage VARCHAR(255),
    imagePath VARCHAR(255) NULL,
    SondageMessage BOOLEAN,
    repostMessage BOOLEAN,
    authorMessage INT,
    dateMessage DATE,
    CONSTRAINT Pk_messages PRIMARY KEY (idMessage),
    CONSTRAINT Fk_messages_authorMessage FOREIGN KEY (authorMessage) REFERENCES USERS(idUser)
);

CREATE TABLE POST_MESSAGES (
    idPost INT,
    idMessage INT,
    CONSTRAINT Pk_post_messages PRIMARY KEY (idPost, idMessage),
    CONSTRAINT Fk_post_messages_post FOREIGN KEY (idPost) REFERENCES POSTS(idPost),
    CONSTRAINT Fk_post_messages_message FOREIGN KEY (idMessage) REFERENCES MESSAGES(idMessage)
);

DROP FUNCTION IF EXISTS GetLikeForPost;
DELIMITER $$
CREATE FUNCTION GetLikeForPost(IdPost INT)
RETURNS INT
BEGIN
    DECLARE nbLike INT;
    SET nbLike = (
        SELECT COUNT(*)
        FROM LIKES AS L
        WHERE L.idPost = IdPost
    );
    RETURN nbLike;
END $$
DELIMITER ;

DROP FUNCTION IF EXISTS GetFollowsForUser;
DELIMITER $$
CREATE FUNCTION GetFollowsForUser(IdUser INT)
RETURNS INT
BEGIN
    DECLARE nbFollows INT;
    SET nbFollows = 0;
    RETURN nbFollows;
END $$
DELIMITER ;

INSERT INTO MOTS_INTERDITS (nomMotInterdit) VALUES
('test1'),
('test2'),
('test3'),
('test4'),
('test5');


-- Remplace les mots interdits par des étoiles

DROP TRIGGER IF EXISTS moderation_message_trigger;

DELIMITER $$

CREATE TRIGGER moderation_message_trigger
BEFORE INSERT ON MESSAGES
FOR EACH ROW
BEGIN
    DECLARE word VARCHAR(50);
    DECLARE done INT DEFAULT 0;

    DECLARE forbidden_words CURSOR FOR 
        SELECT nomMotInterdit 
        FROM MOTS_INTERDITS;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN forbidden_words;
    check_words: LOOP
        FETCH forbidden_words INTO word;
        IF done THEN
            LEAVE check_words;
        END IF;
        SET NEW.textMessage = REPLACE(NEW.textMessage, word, '****');
    END LOOP check_words;
    CLOSE forbidden_words;
END$$

DELIMITER ;


#suppression cascade lorsqu'on delete un post
DROP TRIGGER IF EXISTS cascade_delete_post_messages_trigger;

DELIMITER $$

CREATE TRIGGER cascade_delete_post_messages_trigger
AFTER DELETE ON posts
FOR EACH ROW
BEGIN
    DELETE FROM post_messages WHERE idPost = OLD.idPost;
    DELETE FROM messages WHERE idMessage IN (SELECT idMessage FROM post_messages WHERE idPost = OLD.idPost);
END$$

DELIMITER ;

-- suppression cascade lorsqu'on delete un message
DROP TRIGGER IF EXISTS cascade_delete_post_messages_on_delete_message_trigger;

DELIMITER $$

CREATE TRIGGER cascade_delete_post_messages_on_delete_message_trigger
BEFORE DELETE ON messages
FOR EACH ROW
BEGIN
    DELETE FROM post_messages WHERE post_messages.idMessage = OLD.idMessage;
END$$

DELIMITER ;




-- suppression en cascade lorsqu'on delete un user
DROP TRIGGER IF EXISTS cascade_delete_likes_user_trigger;
DELIMITER $$
CREATE TRIGGER cascade_delete_likes_user_trigger
AFTER DELETE ON USERS
FOR EACH ROW
BEGIN
    DELETE FROM LIKES WHERE idUser = OLD.idUser;
END$$

DELIMITER ;

DROP TRIGGER IF EXISTS cascade_delete_likes_message_trigger;
DELIMITER $$
CREATE TRIGGER cascade_delete_likes_message_trigger
AFTER DELETE ON MESSAGES
FOR EACH ROW
BEGIN
    DELETE FROM LIKES WHERE idMessage = OLD.idMessage;
END$$

DELIMITER ;

-- fonction pour récupérer le nb d'abonnement d'un user
DROP FUNCTION IF EXISTS GetNbAbonnementsForUser;
DELIMITER $$
CREATE FUNCTION GetNbAbonnementsForUser(IdUser INT) RETURNS INT
BEGIN
    DECLARE nbAbonnements INT;

    SELECT COUNT(*) INTO nbAbonnements
    FROM FOLLOWS
    WHERE idUser1 = IdUser;

    RETURN nbAbonnements;
END$$

DELIMITER ;

-- Procedure qui affiche le ou les posts les plus liké pour un thème
DROP PROCEDURE IF EXISTS GetMostLikedPosts;
DELIMITER $$
CREATE PROCEDURE GetMostLikedPosts(IdTheme INT, Limite INT)
BEGIN
    SELECT idPost, idTheme, author, COUNT(likes.idUser) AS nbLikes
    FROM posts
    LEFT JOIN likes ON posts.idPost = likes.idPost
    WHERE posts.idTheme = IdTheme
    GROUP BY posts.idPost
    ORDER BY nbLikes DESC
    LIMIT Limite;
END$$

DELIMITER ;

-- Fonction qui renvoie le nombre de message pour un thème
DROP FUNCTION IF EXISTS GetNbMessagesForTheme;
DELIMITER $$
CREATE FUNCTION GetNbMessagesForTheme(IdTheme INT) RETURNS INT
BEGIN
    DECLARE nbMessages INT;

    SELECT COUNT(*)
    INTO nbMessages
    FROM post_messages
    INNER JOIN posts ON post_messages.idPost = posts.idPost
    WHERE posts.idTheme = IdTheme;

    RETURN nbMessages;
END$$

DELIMITER ;

DROP TRIGGER IF EXISTS CheckDifferentUsersBeforeFollow;
DELIMITER $$
CREATE TRIGGER CheckDifferentUsersBeforeFollow
BEFORE INSERT ON FOLLOWS
FOR EACH ROW
BEGIN
    IF NEW.idUser1 = NEW.idUser2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: idUser1 cannot be equal to idUser2';
    END IF;
END;

DELIMITER ;