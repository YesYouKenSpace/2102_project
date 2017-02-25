DROP TABLE Project;
DROP TABLE Trans;
DROP TABLE Member;
DROP TABLE Role;
DROP TABLE Privilege;

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

CREATE TABLE Member(
	email VARCHAR(64) PRIMARY KEY,
	password VARCHAR(64) NOT NULL,
	country VARCHAR(64) NOT NULL,
	firstName VARCHAR(64) NOT NULL,
	lastName VARCHAR(64) NOT NULL,
	registrationDate DATE NOT NULL,
	roleType VARCHAR(64),
	FOREIGN KEY (roleType) REFERENCES Role(type)
);

CREATE TABLE Trans(
	amount INTEGER NOT NULL,
	transactionNo VARCHAR(64) PRIMARY KEY,
	date DATE NOT NULL,
	email VARCHAR(64),
	projectId SERIAL,
	FOREIGN KEY (email) REFERENCES Member(email),
	FOREIGN KEY (projectId) REFERENCES Project(id)
);

