# BTech 5th Semester DBMS Project
Mock College Database

### Problem Statement
A college contains many departments. Each department can offer any number of courses. Many instructors can work in a department, but an instructor can work only in one department. For each department, there is a head, and an instructor can be head of only one department. Each instructor can take any number of courses, and a course can be taken by only one instructor. A student can enroll for any number of courses and each course can have any number of students. Requirements other than the above can be added.

### Requirement Analysis
* College has several departments.
* College has several instructors.
* Several instructors can work in one department.
* An instructor can work in only one department.
* Every department has a head who is an instructor.
* An instructor can head only one department.
* Each department can offer any number of courses.
* An instructor can only take a course offered by his department.
* Each instructor can take any number of courses.
* A course can be taken by only one instructor.
* College has several students.
* A student can enroll for any number of courses.
* Each course can have any number of students.

### Conceptual & Logical Design of Database
ER diagram and RDBMS schema are generated in [erdplus.com](https://erdplus.com/standalone "ERDplus").

### Proposed Application
* Each user can login using *ID* and *password*.
* Priviledges:
    * *Administrator* can edit all information about *departments*, *courses*, *instructors*, and *students*.
    * *Instructors* can edit attendance and marks of *students* in a *course*.
    * *Students* can view their attendance and marks in each *course*.
