CREATE EXTENSION pgcrypto;

DROP TABLE Trans;
DROP TABLE Project;
DROP TABLE Category;
DROP TABLE Member;
DROP TABLE Role;
DROP TABLE Privilege;
DROP TABLE Country;

CREATE TABLE Privilege(
	privilegeLevel INTEGER PRIMARY KEY,
	canDeleteAll boolean NOT NULL,
	canCreateAll boolean NOT NULL,
	canModifyAll boolean NOT NULL
);
CREATE TABLE Role(
	type VARCHAR(64) PRIMARY KEY,
	privilegeLevel INTEGER,
	FOREIGN KEY (privilegeLevel) REFERENCES Privilege(privilegeLevel)
);

CREATE TABLE Country (
	id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Member(
	email VARCHAR(64) PRIMARY KEY,
	password VARCHAR(64) NOT NULL,
	countryId INTEGER NOT NULL,
	firstName VARCHAR(64) NOT NULL,
	lastName VARCHAR(64) NOT NULL,
	registrationDate DATE NOT NULL,
	roleType VARCHAR(64),
	FOREIGN KEY (roleType) REFERENCES Role(type),
	FOREIGN KEY (countryId) REFERENCES Country(id)
);

CREATE TABLE Category (
	id SERIAL PRIMARY KEY,
	name VARCHAR(64) NOT NULL UNIQUE
)

CREATE TABLE Project (
	id SERIAL PRIMARY KEY,
	email VARCHAR(64),
	title VARCHAR(64) NOT NULL,
	description VARCHAR(64),
	startDate DATE,
	endDate DATE,
	amountFundingSought INTEGER,
	categoryName VARCHAR(64) NOT NULL,
	FOREIGN KEY (categoryName) REFERENCES category(name),
	FOREIGN KEY (email) REFERENCES Member(email),
	CONSTRAINT amountFundingSought CHECK (amountFundingSought>0)
);

CREATE TABLE Trans(
	amount INTEGER NOT NULL,
	transactionNo SERIAL PRIMARY KEY,
	date DATE NOT NULL,
	email VARCHAR(64),
	projectId INTEGER,
	FOREIGN KEY (email) REFERENCES Member(email),
	FOREIGN KEY (projectId) REFERENCES Project(id),
	CONSTRAINT amount CHECK (amount>0)
);

