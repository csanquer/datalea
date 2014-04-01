# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO `Entity_User` (`person-name-firstname`, `person-name-lastname`, `person-email`, `birthday`) VALUES
('Adolph', 'McCullough', 'adolph.mccullough@yahoo.com', '1994-05-30'),
('Sebastian', 'Harvey', 'sebastian.harvey@yahoo.com', '1927-10-02'),
('Norris', 'Douglas', 'norris.douglas@hotmail.com', '1994-08-12');

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
