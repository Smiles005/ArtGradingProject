from django.db import models
from django.core.validators import MaxValueValidator, MinValueValidator
from django.urls import reverse

class Student(models.Model):
    studentID = models.IntegerField(validators = [MinValueValidator(0)], default = 0, unique = True)
    name = models.CharField(max_length = 256, default = "Student Name")
    email = models.CharField(max_length = 128, default = "Student Email Address")

    class Meta:
        constraints = [models.UniqueConstraint(fields = ['name', 'email'], name = 'unique_student_name_email')]

    def createStudent(studentID, name, email):
        student1 = Student(studentID = studentID, name = name, email = email)
        student1.save()
        return student1

    def modifyStudent(ID, studentID, name, email):
        student = Student.objects.get(id = ID)
        student.studentID = studentID
        student.name = name
        student.email = email
        student.save()

    def serialize(self):
        return {
            "studentID": self.studentID,
            "name": self.name,
            "email": self.email,
            "pathToStudentPage": reverse("specificStudentView", args=[self.id])
        }

class Assignment(models.Model):
    name = models.CharField(max_length = 128, default = "Assignment Name")
    course = models.ForeignKey("Course", on_delete = models.PROTECT, null = True) # Null = True because there is no appropriate default and Django won't let me have no default and Null = False.
    students = models.ManyToManyField(Student)

    class Meta:
        constraints = [
            models.UniqueConstraint(fields = ['name', 'course'], name = 'unique_assignment_name_course')
        ]

    def removeProject(self, project):
        projectsToRemove = Project.objects.filter(assignment = self)
        for project in projectsToRemove:
            project.delete()

    def serialize(self):
        return {
            "name": self.name,
            "courseName": self.course.name if self.course is not None else "",
            "pathToAssignmentPage": reverse("specificAssignmentView", args=[self.id])
        }

class Course(models.Model):
    name = models.CharField(max_length = 128, default = "Course Name")
    courseCode = models.CharField(max_length = 16, default = "CCCC-1234")
    semester = models.CharField(max_length = 16, default = "Fall") # Can switch default if desired.
    year = models.IntegerField(validators = [MinValueValidator(2022), MaxValueValidator(9999)], default = 1858)
    students = models.ManyToManyField(Student)

    class Meta:
        constraints = [
            models.UniqueConstraint(fields = ['name', 'courseCode', 'semester', 'year'], name = 'unique_course_name_courseCode_semester_year')
        ]

    def addAssignment(self, assignment):
        newProject = Project(isTemplate = True, course = self, assignment = assignment)
        newProject.save()

    def addStudent(self, student):
        self.students.add(student)

    def removeAssignment(self, assignment):
        projectsToDelete = Project.objects.filter(course = self, assignment = assignment, isTemplate = False)
        for project in projectsToDelete:
            project.delete()

    def removeStudent(self, student):
        self.students.remove(student) 

    def serialize(self):
        return {
            "name": self.name,
            "courseCode": self.courseCode,
            "semester": self.semester,
            "year": self.year,
            "pathToCoursePage": reverse("specificCourseView", args=[self.id])
        }

class Project(models.Model):
    name = models.CharField(max_length = 128, default = "Project Name")
    isTemplate = models.BooleanField(default = False) 
    student = models.ForeignKey("Student", on_delete = models.PROTECT, null = True)
    course = models.ForeignKey("Course", on_delete = models.PROTECT, null = True)
    assignment = models.ForeignKey("Assignment", on_delete = models.PROTECT, null = True) # Null = True because there is no appropriate default and Django won't let me have no default and Null = False.
    numTables = models.IntegerField(validators = [MinValueValidator(0)], default = 0)
    numSliders = models.IntegerField(validators = [MinValueValidator(0)], default = 0)

    def cloneProject(student, course, assignment):
        project1 = Project(student, course, assignment)
        project1.save()

    def createProject():
        project1 = Project()
        project1.save()
        return project1

    def modifyProject(self, student, course, assignment):
        self.student = student
        self.course = course
        self.assignment = assignment
        self.save()

    def deleteProject(self):
        self.delete()

    def serialize(self):
        return {
            "name": self.name,
            "isTemplateString": "(Not a Template)" if self.isTemplate == False else "(Template)",
            "studentString": "Student: " + self.student.name if self.student is not None else "",
            "courseString": "Course: " + self.course.name if self.course is not None else "",
            "assignmentString": "Assignment: " + self.assignment.name if self.assignment is not None else "",
            "pathToProjectPage": reverse("specificProjectView", args=[self.id])
        }