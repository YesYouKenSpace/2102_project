## The members who have not invested
SELECT *
FROM Member m1
WHERE NOT EXISTS(SELECT *
FROM Trans T1
WHERE M1.email== T1.email);

## Members who have not invested for more than 30 days and are more than 30 days old user and their last transaction date and total donation

SELECT m1.firstName, m1.lastName, MAX(t2.date) AS latesttrans, SUM(t2.amount) AS donation, m1.email
FROM Member m1 NATURAL JOIN Trans t2
WHERE NOT EXISTS(SELECT *
FROM Trans T1
WHERE M1.email=T1.email AND current_date-T1.date < 30)
GROUP BY m1.firstName,m1.lastName, m1.email
ORDER BY latesttrans;

## Projects that got funded fully already

SELECT P1.title, P1.categoryName, P1.amountFundingSought, totalsum
FROM Project P1
WHERE EXISTS
(SELECT ProjectId as pid, SUM(T1.amount) AS totalsum
FROM Trans T1
WHERE SUM(T1.amount) >= P1.amountFundingSought
GROUP BY T1.projectId);

## Top 100 investors ALL TIME
SELECT *
FROM Member m1 NATURAL JOIN
(SELECT T1.email, SUM(T1.amount) AS totalsum
FROM Trans T1
GROUP BY T1.email)
ORDER BY totalsum DESC
LIMIT 100 OFFSET 0;

## Top 100 investors in a week (not THE week)
SELECT *
FROM Member m1 NATURAL JOIN
(SELECT T1.email, SUM(T1.amount) AS totalsum
FROM Trans T1
WHERE T1.date - getDate() <=7
GROUP BY T1.email)
ORDER BY totalsum DESC
LIMIT 100 OFFSET 0;

##
