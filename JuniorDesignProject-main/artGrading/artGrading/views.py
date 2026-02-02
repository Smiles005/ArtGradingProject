from .artQuotes import getArtQuote
from .models import Assignment, Course, Project, Student
from .otherClasses import DatabaseView, SearchResults, HomeScreen
from ast import literal_eval
from django.http import HttpResponseRedirect, Http404, JsonResponse
from django.shortcuts import render
from django.urls import reverse
import json, os

def addItem(request, itemType):
    if(itemType == "student"):
        newStudentName = request.POST.get("studentName")
        newStudentEmail = request.POST.get("email")
        newStudentIDRaw = request.POST.get("studentID")
        newStudentCourseID = request.POST.get("course")
        
        try:
            newStudentID = int(newStudentIDRaw)
            if(newStudentID < 0): 
                raise ValueError # Because the student ID can't be negative
            # We must save the student (done in this function call) before adding the student to the course
            newStudent = Student.createStudent(studentID = int(newStudentID), name = newStudentName, email = newStudentEmail) 
            studentCourse = Course.objects.get(id = newStudentCourseID)
            studentCourse.students.add(newStudent)
        except ValueError: # The user submitted an invalid (text or non-negative integer or floating-point number) student ID
            return errorView(request, message = f"You cannot add a student with an invalid (text, negative integer, or non-integer (floating point) number) student ID: {newStudentID}.")
        except Course.DoesNotExist:
            return errorView(request, message = f"Error: the course with ID {newStudentCourseID} does not exist. Please try to add this student again with an existing course.")

        return HttpResponseRedirect(reverse('specificStudentView', args=[newStudent.id]))
    elif(itemType == "course"):
        newCourseName = request.POST.get("name")
        newCourseCode = request.POST.get("courseCode")
        newCourseSemester = request.POST.get("semester")

        try:
            newCourseYearRaw = request.POST.get("year")
            newCourseYear = int(newCourseYearRaw)
            newCourse = Course(name = newCourseName, courseCode = newCourseCode, semester = newCourseSemester, year = newCourseYear)
            newCourse.save() # We need to save this before adding the students

            IDsOfStudentsToAdd = request.POST.getlist("students", "")
            if(len(IDsOfStudentsToAdd) > 0): # If students were added in the creation of this course
                for studentID in IDsOfStudentsToAdd: # Try to add the student with that ID
                    try:
                        newCourse.addStudent(Student.objects.get(id = studentID)) 
                        # If the student is already in the course, they aren't in this twice after this addStudent call
                    except Student.DoesNotExist: 
                        pass # Do nothing, because we can't add a student that doesn't exist
            
            return HttpResponseRedirect(reverse('specificCourseView', args=[newCourse.id]))
        except ValueError: # The passed year wasn't an integer
            return errorView(request, message = f"You cannot add a course with an invalid year: {newCourseYearRaw}.")            
    elif(itemType == "assignment"):
        newAssignmentName = request.POST.get("name")
        newAssignmentCourseID = request.POST.get("course")
        try:
            newAssignmentCourse = Course.objects.get(id = newAssignmentCourseID)
            newAssignment = Assignment(name = newAssignmentName, course = newAssignmentCourse)
            newAssignment.save() # We need to save this before adding the students

            return HttpResponseRedirect(reverse('specificAssignmentView', args=[newAssignment.id]))
        except Course.DoesNotExist:
            return errorView(request, message = f"Error: the course with ID {newAssignmentCourseID} does not exist. Please try to add this assignment again with an existing course.")
    elif(itemType == "project"):
        try:
            newProjectIsTemplate = True if request.POST.get("isTemplate", "") == "on" else False
            newProjectStudentID = request.POST.get("student")
            newProjectCourseID = request.POST.get("course")
            newProjectAssignmentID = request.POST.get("assignment")
            newProjectNumTablesRaw = request.POST.get("numOfTables")
            newProjectNumSlidersRaw = request.POST.get("numOfSliders")
            newProjectNumTables = int(newProjectNumTablesRaw) if newProjectNumTablesRaw != "" else 0 
            newProjectNumSliders = int(newProjectNumSlidersRaw) if newProjectNumSlidersRaw != "" else 0

            newProjectStudent = Student.objects.get(id = newProjectStudentID)
            newProjectCourse = Course.objects.get(id = newProjectCourseID)
            newProjectAssignment = Assignment.objects.get(id = newProjectAssignmentID)

            newProject = Project.createProject()

            newProject.isTemplate = newProjectIsTemplate
            newProject.student = newProjectStudent
            newProject.assignment = newProjectAssignment
            newProject.course = newProjectCourse
            newProject.assignment = newProjectAssignment
            newProject.numTables = newProjectNumTables
            newProject.numSliders = newProjectNumSliders

            newProject.save()

            return HttpResponseRedirect(reverse('specificProjectView', args=[newProject.id]))
        except (Student.DoesNotExist, Course.DoesNotExist, Assignment.DoesNotExist): 
            return errorView(request, message = f"Error: Either the student (ID {newProjectStudentID}), course (ID {newProjectCourseID}), and/or assignment (ID {newProjectAssignmentID}) with the specified ID does not exist. Please try to add this project again with an existing student, an existing assignment, and and existing project.")

def assignmentView(request):
    assignments = Assignment.objects.all()
    return DatabaseView.viewItems(items = assignments, itemType = "assignment", request = request)

def specificAssignmentView(request, assignmentID):
    try:
        assignment = Assignment.objects.get(id = assignmentID)
        associatedCourse = assignment.course
        associatedProjects = Project.objects.filter(assignment = assignment)
        associatedStudents = Student.objects.filter(id__in = associatedProjects.values('student__id'))
        return SearchResults.viewItem(item = assignment, itemType = "assignment", 
                                      associatedItems = {"associatedCourse": associatedCourse, "associatedProjects": associatedProjects,
                                                         "associatedStudents": associatedStudents}, request = request)
    except Assignment.DoesNotExist:
        return notFoundView(request, Http404())

def courseView(request):
    courses = Course.objects.all()
    return DatabaseView.viewItems(items = courses, itemType = "course", request = request)

def specificCourseView(request, courseID):
    try:
        course = Course.objects.get(id = courseID)
        associatedStudents = course.students.all()
        associatedProjectsSearchResult = Project.objects.filter(course = course)
        associatedAssignments = Assignment.objects.filter(course = course)
        associatedProjects = [{} for i in range(associatedAssignments.count())]
        index = -1
        for assignment in associatedAssignments:
            index += 1
            associatedProjects[index]['name'] = assignment.name
            associatedProjects[index]['id'] = assignment.id
            associatedProjects[index]['projects'] = {}
            associatedProjectsSearchResult = Project.objects.filter(course = course, assignment = assignment)
            for project in associatedProjectsSearchResult:
                associatedProjects[index]['projects'] = {}
                associatedProjects[index]['projects'][f"{project.id}"] = project

        return SearchResults.viewItem(item = course, itemType = "course", associatedItems = {"associatedStudents": associatedStudents, 
                                                                                             "associatedProjects": associatedProjects,
                                                                                             "associatedAssignments": associatedAssignments}, request = request)
    except Course.DoesNotExist:
        return notFoundView(request, Http404())

def databaseView(request):
    courses = Course.objects.all()
    assignments = Assignment.objects.all()
    students = Student.objects.all()
    projects = Project.objects.all()
    return DatabaseView.viewItems(items = {"students": students, "courses": courses, 
                                           "projects": projects, "assignments": assignments}, itemType = "databaseView", request = request)

def errorView(request, message):
    return render(request, os.path.join("artGrading", "error.html"), {"message": message})

def greetingPage(request):
    artQuoteList = getArtQuote()
    return render(request, os.path.join("artGrading", "greeting.html"), {"artQuote": artQuoteList[0], "artQuoteAuthor": artQuoteList[1], "statusCode": 200 })

def homepage(request):
    return render(request, os.path.join("artGrading", "homepage.html"))

def notFoundView(request, exception):
    return render(request, os.path.join("artGrading", "404.html"), status = 404)

def projectView(request):
    projects = Project.objects.all()
    return DatabaseView.viewItems(items = projects, itemType = "project", request = request)

def removeItem(request, itemType, itemID):
    deletionSuccessful = False
    if(itemType == "student"):
        try:
            studentToDelete = Student.objects.get(id = itemID)

            # We only need to check project because a student can be removed from a course but can't be removed from a project instance
            if(Project.objects.filter(student__in = [studentToDelete]).count() == 0): 
                studentToDelete.delete()
                deletionSuccessful = True
            else:
                errorMessage = f"Could not delete the student with ID {itemID} because that student has projects saved in the system."
        except Student.DoesNotExist: # Only set error message, since we can't delete a student that doesn't exist
            errorMessage = f"The student with ID {itemID} that you tried to delete does not exist."
    elif(itemType == "project"):
        try:
            projectToDelete = Project.objects.get(id = itemID)

            # We don't need to check anything before deleting a project
            projectToDelete.deleteProject()
            deletionSuccessful = True
        except Project.DoesNotExist: # Only set error message, since we can't delete a project that doesn't exist
            errorMessage = f"The project with ID {itemID} that you tried to delete does not exist."
    elif(itemType == "course"):
        try:
            courseToDelete = Course.objects.get(id = itemID)

            # Don't need to check projects because projects shouldn't exist without assignments
            if(Assignment.objects.filter(course = courseToDelete).count() == 0):
                courseToDelete.delete()
                deletionSuccessful = True
            else:
                errorMessage = f"Could not delete the course with ID {itemID} because that course has associated students and or projects saved in the system."
        except Course.DoesNotExist: # Only set error message, since we can't delete a course that doesn't exist
            errorMessage = f"The course with ID {itemID} that you tried to delete does not exist."
    elif(itemType == "assignment"):
        try:
            assignmentToDelete = Assignment.objects.get(id = itemID)
            projectsWithAssignment = Project.objects.filter(assignment = assignmentToDelete)
            coursesWithAssignment = projectsWithAssignment.values('course')
            if(projectsWithAssignment.count() == 0 and coursesWithAssignment.count() == 0):
                assignmentToDelete.delete()
                deletionSuccessful = True
            else:
                errorMessage = f"Could not delete the assignment with ID {itemID} because that assignment has associated projects and or courses saved in the system."
        except Project.DoesNotExist: # Only set error message, since we can't delete a project that doesn't exist
            errorMessage = f"The assignment with ID {itemID} that you tried to delete does not exist."
    if(deletionSuccessful == True):
        return HttpResponseRedirect(reverse("homepage"))
    else:
        return HttpResponseRedirect(reverse("errorView", args=[errorMessage]))

def specificProjectView(request, projectID):
    try:
        project = Project.objects.get(id = projectID)
        associatedCourse = project.course
        associatedStudents = associatedCourse.students.all()
        associatedAssignment = project.assignment
        return SearchResults.viewItem(item = project, itemType = "project", associatedItems = {"associatedStudents": associatedStudents, 
                                                                                               "associatedCourse": associatedCourse,
                                                                                               "associatedAssignment": associatedAssignment}, request = request)
    except Project.DoesNotExist:
        return notFoundView(request, Http404())
    


def searchResults(request):
    searchParameter = request.POST.get("searchParameter", "")
    return render(request, os.path.join("artGrading", "search-results.html"), {"searchResults": SearchResults.search(searchParameter), "searchParameter": searchParameter})

def searchResultsDropdown(request):
    searchParameter = json.loads(request.body).get("searchParameter", "")
    searchResultsDictionaries = SearchResults.search(searchParameter)
    searchResultsDictionaries["assignments"] = list(map(mapItem, searchResultsDictionaries["assignments"]))
    searchResultsDictionaries["courses"] = list(map(mapItem, searchResultsDictionaries["courses"]))
    searchResultsDictionaries["projects"] = list(map(mapItem, searchResultsDictionaries["projects"]))
    searchResultsDictionaries["students"] = list(map(mapItem, searchResultsDictionaries["students"]))

    return JsonResponse({"searchResults": searchResultsDictionaries})

def mapItem(item):
    return item.serialize()

def studentView(request):
    students = Student.objects.all()
    return DatabaseView.viewItems(items = students, itemType = "student", request = request)

def specificStudentView(request, studentID):
    try:
        student = Student.objects.get(id = studentID)
        associatedProjects = Project.objects.filter(student = student)
        associatedAssignments = Assignment.objects.filter(id__in = associatedProjects.values('assignment__id'))
        associatedCourses = Course.objects.filter(students = student)
        return SearchResults.viewItem(item = student, itemType = "student", associatedItems = {"associatedProjects": associatedProjects, 
                                                                                               "associatedAssignments": associatedAssignments,
                                                                                               "associatedCourses": associatedCourses}, request = request)
    except Student.DoesNotExist:
        return notFoundView(request, Http404())

def createStudentView(request):
    courses = Course.objects.all()
    return render(request, os.path.join("artGrading", "create-student.html"), {"courses": courses})

def createProjectView(request):
    student = Student.objects.all()
    courses = Course.objects.all()
    assignments = Assignment.objects.all()
    return render(request, os.path.join("artGrading","create-project.html"), {"students": student,"courses": courses, "assignments": assignments})

def projectEditView(request, projectID):
    try:
        project = Project.objects.get(id = projectID)
        associatedCourse = project.course
        associatedStudents = associatedCourse.students.all()
        associatedAssignment = project.assignment
        return render(request, os.path.join("artGrading","project-edit.html"),  {"associatedStudents": associatedStudents, 
                                                                                 "associatedCourse": associatedCourse,
                                                                                 "associatedAssignment": associatedAssignment,
                                                                                 "project": project})
    
    except Project.DoesNotExist:
        return notFoundView(request, Http404())