-- Database: quiz

-- Table structure for table admin

CREATE TABLE admin (
  admin_id SERIAL PRIMARY KEY,
  email VARCHAR(50) NOT NULL,
  password VARCHAR(500) NOT NULL
);

-- Dumping data for table admin

INSERT INTO admin (admin_id, email, password) VALUES
(1, 'admin@kweez.com', 'admin');

-- Table structure for table answer

CREATE TABLE answer (
  qid TEXT NOT NULL,
  ansid TEXT NOT NULL
);

-- Table structure for table history

CREATE TABLE history (
  email VARCHAR(50) NOT NULL,
  eid TEXT NOT NULL,
  score INTEGER NOT NULL,
  level INTEGER NOT NULL,
  sahi INTEGER NOT NULL,
  wrong INTEGER NOT NULL,
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table structure for table options

CREATE TABLE options (
  qid VARCHAR(50) NOT NULL,
  option VARCHAR(5000) NOT NULL,
  optionid TEXT NOT NULL
);

-- Table structure for table questions

CREATE TABLE questions (
  eid TEXT NOT NULL,
  qid TEXT NOT NULL,
  qns TEXT NOT NULL,
  choice INTEGER NOT NULL,
  sn INTEGER NOT NULL
);

-- Table structure for table quiz

CREATE TABLE quiz (
  eid TEXT NOT NULL,
  title VARCHAR(100) NOT NULL,
  sahi INTEGER NOT NULL,
  wrong INTEGER NOT NULL,
  total INTEGER NOT NULL,
  time BIGINT NOT NULL,
  intro TEXT NOT NULL,
  tag VARCHAR(100) NOT NULL,
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table structure for table rank

CREATE TABLE rank (
  email VARCHAR(50) NOT NULL,
  score INTEGER NOT NULL,
  time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table structure for table user
-- Note: user is a reserved keyword, so we quote it here

CREATE TABLE "user" (
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL PRIMARY KEY,
  password VARCHAR(50) NOT NULL
);
