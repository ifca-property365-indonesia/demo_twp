sp_configure 'allow updates', 1
go
reconfigure with override
go
insert into syslanguages values (8,'dmy', 1,6,'IFCA', 'IFCA','January,February,March,April,May,June,July,August,September,October,November,December','Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec','Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday')
go
sp_configure 'default language', 8
go
sp_configure 'allow updates', 0
go
reconfigure with override
go
sp_configure
go