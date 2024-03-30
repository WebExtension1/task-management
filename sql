CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` text NOT NULL,
  `surname` text NOT NULL,
  `email` text NOT NULL,
  `psword` text NOT NULL,
    PRIMARY KEY (userID)
);

CREATE TABLE `tasks` (
  `taskID` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` text NOT NULL,
    PRIMARY KEY (taskID)
);

CREATE TABLE `tasklists` (
  `taskListID` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
    PRIMARY KEY (taskListID)
);

CREATE TABLE `taskcomment` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `taskID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
    PRIMARY KEY (commentID),
    FOREIGN KEY (taskID) REFERENCES tasks (taskID),
    FOREIGN KEY (userID) REFERENCES users (userID)
);

CREATE TABLE `notification` (
  `notificationID` int(11) NOT NULL AUTO_INCREMENT,
  `associatedTask` int(11) NOT NULL,
  `description` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (notificationID),
    FOREIGN KEY (associatedTask) REFERENCES tasks (taskID)
);

CREATE TABLE `tasklistaccess` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `taskListID` int(11) NOT NULL,
  `colour` text NOT NULL,
  `owner` tinyint(1) NOT NULL,
    FOREIGN KEY (userID) REFERENCES users (userID),
    FOREIGN KEY (taskListID) REFERENCES tasklists (taskListID)
);

CREATE TABLE `taskcompleted` (
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `taskID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
    FOREIGN KEY (taskID) REFERENCES tasks (taskID),
    FOREIGN KEY (userID) REFERENCES users (userID)
);

CREATE TABLE `tasklisttasks` (
  `taskListID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
    FOREIGN KEY (taskListID) REFERENCES taskLists (taskListID),
    FOREIGN KEY (taskID) REFERENCES tasks (taskID)
);

INSERT INTO `taskcomment` (`commentID`, `text`, `timestamp`, `taskID`, `userID`) VALUES
(1, 'First comment', '2024-02-27 22:01:14', 1, 1),
(2, 'Second comment', '2024-02-27 22:02:32', 1, 1);

INSERT INTO `tasklistaccess` (`userID`, `taskListID`, `colour`, `owner`) VALUES
(1, 1, 'Blue', 1),
(1, 2, 'Red', 1);

INSERT INTO `tasklists` (`taskListID`, `name`) VALUES
(1, 'Main'),
(2, 'Work');

INSERT INTO `tasklisttasks` (`taskListID`, `taskID`) VALUES
(1, 1),
(1, 2),
(2, 3),
(2, 4);

INSERT INTO `tasks` (`taskID`, `name`, `description`, `status`) VALUES
(1, 'First Task', 'First task description', 'open'),
(2, 'Second Task', 'Second task description', 'open'),
(3, 'Third Task', 'Third task description', 'open'),
(4, 'Fourth Task', 'Fourth task description', 'open');

INSERT INTO `users` (`userID`, `firstname`, `surname`, `email`, `psword`) VALUES
(1, 'Robert', 'Jenner', 'robertjenner5@me.com', '12345');