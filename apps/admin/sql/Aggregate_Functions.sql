## The members who have not invested
SELECT *
FROM Member m1
WHERE NOT EXISTS(SELECT * 
FROM Trans T1
WHERE M1.email== T1.email);

## Members who have not invested for a certain time(365 days)

SELECT *
FROM Member m1
WHERE NOT EXISTS(SELECT *
FROM Trans T1
WHERE M1.email==T1.email AND T1.date-getDate() < 365);

## Projects that got funded fully already

SELECT P1.title, P1.categoryName, P1.amountFundingSought, totalsum
FROM Project P1
WHERE EXISTS
(SELECT ProjectId as pid, SUM(amount) AS totalsum
FROM Trans T1
WHERE SUM(amount) >= P1.amountFundingSought
GROUP BY projectId);

