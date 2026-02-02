"""artGrading URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/4.1/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.contrib import admin
from django.urls import include, path
from . import views
from .settings.base import inProductionMode

urlpatterns = [
    path('admin/', admin.site.urls),
    path('add-item/<str:itemType>', views.addItem, name = 'addItem'),
    path('assignment', views.assignmentView, name = 'assignmentView'),
    path('assignment/<int:assignmentID>', views.specificAssignmentView, name = 'specificAssignmentView'),
    path('course', views.courseView, name = 'courseView'),
    path('course/<int:courseID>', views.specificCourseView, name = 'specificCourseView'),
    path('database', views.databaseView, name = 'databaseView'),
    path('', views.homepage, name = 'homepage'),
    path('greet', views.greetingPage, name = "greetingPage"),
    path('project', views.projectView, name = 'projectView'),
    path('project/<int:projectID>', views.specificProjectView, name = 'specificProjectView'),
    path('search-results', views.searchResults, name = 'searchResults'),
    path('search-results-dropdown', views.searchResultsDropdown, name = 'searchResultsDropdown'),
    path('student', views.studentView, name = 'studentView'),
    path('student/<int:studentID>', views.specificStudentView, name = 'specificStudentView'),
    path('create-student', views.createStudentView, name = 'createStudent'),
    path('create-project', views.createProjectView, name = 'createProject'),
    path('project-edit/<int:projectID>', views.projectEditView, name = 'projectEdit'),
    path('remove/<str:itemType>/<int:itemID>', views.removeItem, name = 'removeItem'),
    path('error/<str:message>', views.errorView, name = 'errorView')
]

if(inProductionMode() == True):
    urlpatterns.append(path('oauth2/', include('django_auth_adfs.urls')))

handler404 = 'artGrading.views.notFoundView'