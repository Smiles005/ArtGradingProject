const COURSE_SELECT = document.querySelector("#course");
const ASSIGNMENT_SELECT_ASSIGNMENTS = document.querySelector("#assignment").children;

COURSE_SELECT.addEventListener("change", function() {
    let courseID = this.value;

    for(let assignmentOption of ASSIGNMENT_SELECT_ASSIGNMENTS) {
        // Display the assignment if it's for the selected course, otherwise hide it
        if(!assignmentOption.classList.contains(courseID)) {
            assignmentOption.style.display = "none";
        } else {
            assignmentOption.style.display = "inline-block";
        }
    }
});


//method 1 
const container = document.getElementById('input-cont');
var maxInputAllowed = 5;
var inputCount = 0;

// Call addInput() function on button click
// function addInput(){
//     inputCount++; // Increment input count by one
//     if(inputCount>5){
//         alert('You can add maximum 5 input fields.');
//         return;
//     }
//     let input = document.createElement('input');
//     input.placeholder = 'Type something';
//     container.appendChild(input);   
// }
// function addInputField() {
//     countre += 1

//     html = 
//     '<div class="form-group row"> \
//             <div class="col-sm-10">\
//                 <button type="button" class="btn btn-primary">Add Rubric</button> \
//                 <!-- Insert code to add input field --> \
//             </div>\
//         </div>'
        
//     var form = document.getElementById('form-horizontal')
//     form.innerHTML =+ html
// }

//Daniel's event listener method
function addKeywordFields() {
    console.log("Hello")
    var mydiv = document.getElementById("fieldContainer");
    const input = document.createElement('input');
    input.type = 'text';
    input.name = (new Date()).toISOString(); // Some dynamic name logic
    mydiv.appendChild(input);
}


