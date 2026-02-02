from .models import *
from django.db.models import Q, TextField
from django.db.models.functions import Cast
from django.shortcuts import render
import os

class DatabaseView():
    def viewItems(items, itemType, request):
        if(itemType == "student"):
            return render(request, os.path.join("artGrading", "database-item-view.html"), {"items": items, "itemType": itemType})
        elif(itemType == "course"):
            return render(request, os.path.join("artGrading", "database-item-view.html"), {"items": items, "itemType": itemType, "students": Student.objects.all()})
        elif(itemType == "assignment"):
            return render(request, os.path.join("artGrading", "database-item-view.html"), {"items": items, "itemType": itemType, "courses": Course.objects.all()})
        elif(itemType == "project"):
            return render(request, os.path.join("artGrading", "database-item-view.html"), {"items": items, "itemType": itemType})
        elif(itemType == "databaseView"):
            return render(request, os.path.join("artGrading", "database-item-view.html"), {"items": items, "itemType": itemType})
            

class SearchResults():
    def search(searchParameter):
        # Note: contains is case insensitive (for example, "name__contains = 'Bob' matches names of 'Bob' and 'bob')
        assignments = Assignment.objects.filter(name__contains = searchParameter)
        courses = Course.objects.filter(Q(name__contains = searchParameter) | Q(courseCode__contains = searchParameter) | Q(semester__contains = searchParameter))
        projects = Project.objects.filter(name__contains = searchParameter)
        if(searchParameter.lower() == "template"):
            projects = projects.filter(isTemplate = True)
        students = Student.objects.filter(Q(name__contains = searchParameter) | Q(email__contains = searchParameter))

        # If the parameter was an integer as a string, then search the integer fields by a variable whos value is that string casted to an integr
        try:
            searchParameterAsInt = int(searchParameter)
            students = students.annotate(studentID_as_text = Cast('studentID', TextField()))
            students = students.filter(studentID_as_text__contains = Cast(searchParameter))
            courses = courses.annotate(year_as_text = Cast('year', TextField()))
            courses = courses.filter(year_as_text__contains = Cast(searchParameter))
        except ValueError: # The user did not pass an integer (in the form of a string)
            pass 

        return {"assignments": assignments.all(), "courses": courses.all(),  "projects": projects.all(), "students": students.all()}

    def viewItem(item, itemType, associatedItems, request):
        if(itemType == "student"):
            return render(request, os.path.join("artGrading", "item-view.html"), {"item": item, "itemType": itemType, "associatedItems": associatedItems})
        elif(itemType == "course"):
            return render(request, os.path.join("artGrading", "item-view.html"), {"item": item, "itemType": itemType, "associatedItems": associatedItems})
        elif(itemType == "assignment"):
            return render(request, os.path.join("artGrading", "item-view.html"), {"item": item, "itemType": itemType, "associatedItems": associatedItems})
        elif(itemType == "project"):
            return render(request, os.path.join("artGrading", "item-view.html"), {"item": item, "itemType": itemType, "associatedItems": associatedItems})

class HomeScreen(SearchResults):
    pass
    # Inherits the view and search methods from the SearchResults class
