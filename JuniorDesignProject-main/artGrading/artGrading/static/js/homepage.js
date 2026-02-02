document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('myInput');
    const searchButton = document.querySelector('#search-button');
    const dropdown = document.querySelector('#myDropdown');
    const body = document.querySelector("body");

    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            search_focus();
            dropdown_search(this.value);
        });

        searchInput.addEventListener("click", function() {
            search_focus();
        });
       

        document.addEventListener("click", function() {
            if(!dropdown.contains(document.activeElement) && document.activeElement !== searchInput) dropdown.style.display = "none";
        });
    }

    if(searchButton && searchInput) {
        searchInput.addEventListener("keyup", function(event) {
            if(event.code === 'Enter') searchButton.click();
        });
    }
});

function search_focus() {
    let input = document.getElementById('myInput').value;
    let x = document.getElementsByClassName('results');
    let drpdwn  = document.getElementById('myDropdown');
    let displayOptionsDiv = new Boolean(false);
    
    // input = input.toLowerCase();
    if (input){
        for (i = 0; i < x.length; i++) { 
            if (!x[i].innerHTML.toLowerCase().includes(input.toLowerCase())) {
                x[i].style.display="none";
            }
            else {
                displayOptionsDiv = true;
                x[i].style.display="list-item";
            }
        }
    }

    if(!displayOptionsDiv) {
        drpdwn.style.display="none";
    } else {
        drpdwn.style.display = "list-item";
    }
    // return x;
}

function dropdown_search(searchParameter) {
    const CSRF_TOKEN = document.querySelector("[name='csrfmiddlewaretoken']").value;

    const REQUEST = new Request('/search-results-dropdown', {
        method: "POST",
        headers: {'X-CSRFToken': CSRF_TOKEN},
        body: JSON.stringify({"searchParameter": searchParameter})
    });
    fetch(REQUEST, {
        mode: 'same-origin'
    })
    .then(response => response.json()
    .then(result => {
        let dropdownAssignmentsDiv = document.querySelector("#dropdownAssignments");
        let dropdownCoursesDiv = document.querySelector("#dropdownCourses");
        let dropdownProjectsDiv = document.querySelector("#dropdownProjects");
        let dropdownStudentsDiv = document.querySelector("#dropdownStudents");

        dropdownAssignmentsDiv.innerHTML = "";
        dropdownCoursesDiv.innerHTML = "";
        dropdownProjectsDiv.innerHTML = "";
        dropdownStudentsDiv.innerHTML = "";

        result["searchResults"]["assignments"].forEach(function(assignment) {
            dropdownAssignmentsDiv.append(createItemLink(assignment, "assignment"));
        });

        result["searchResults"]["courses"].forEach(function(course) {
            dropdownCoursesDiv.append(createItemLink(course, "course"));
        });
        
        result["searchResults"]["projects"].forEach(function(project) {
            dropdownProjectsDiv.append(createItemLink(project, "project"));
        });

        result["searchResults"]["students"].forEach(function(student) {
            dropdownStudentsDiv.append(createItemLink(student, "student"));
        });
    }));
}

function createItemLink(itemDictionary, itemType) {
    if(itemType === "assignment") {
        let newAssignmentLink = document.createElement("a");
        newAssignmentLink.setAttribute("href", itemDictionary["pathToAssignmentPage"]);
        newAssignmentLink.innerHTML = `Name: ${itemDictionary["name"]} Course: ${itemDictionary["courseName"]}`;
        addLinkEventListener(newAssignmentLink);
        return newAssignmentLink;
    } else if(itemType === "course") {
        let newCourseLink = document.createElement("a");
        newCourseLink.setAttribute("href", itemDictionary["pathToCoursePage"]);
        newCourseLink.innerHTML = `${itemDictionary["courseCode"]}: ${itemDictionary["name"]} -- ${itemDictionary["semester"]} ${itemDictionary["year"]}`;
        addLinkEventListener(newCourseLink);
        return newCourseLink;
    } else if(itemType === "project") {
        let newProjectLink = document.createElement("a");
        newProjectLink.setAttribute("href", itemDictionary["pathToProjectPage"]);
        newProjectLink.innerHTML = `Name: ${itemDictionary["name"]} ${itemDictionary["isTemplateString"]}<br>
                                    Student: ${itemDictionary["studentString"]}<br>
                                    Course: ${itemDictionary["courseString"]}<br>
                                    Assignment: ${itemDictionary["assignmentString"]}`;
        addLinkEventListener(newProjectLink);
        return newProjectLink;
    } else if(itemType === "student") {
        let newStudentLink = document.createElement("a");
        newStudentLink.setAttribute("href", itemDictionary["pathToStudentPage"]);
        newStudentLink.innerHTML = `${itemDictionary["name"]}`;
        addLinkEventListener(newStudentLink);
        return newStudentLink;
    }
}

function addLinkEventListener(link) {
    link.addEventListener("click", function() {
        this.classList.add("clickedLink");
    });
}