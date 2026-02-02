var numofSliders = 0;
var numofTables = 0;

function createTable(rows, colmns, elem){
    const deleteButton = document.createElement("button");
    const addRow = document.createElement("button");
    const addColumn = document.createElement("button");
    const divBlock = document.createElement("div");
    const table = document.createElement('TABLE');

    let thead = table.createTHead();
    let row = thead.insertRow();
    for (let i = 0; i < colmns; i++){
        let th = document.createElement("th");
        let input = document.createElement("INPUT");
        th.appendChild(input);
        row.appendChild(th);
    }

    for (let i = 0; i < rows; i++) {
        const tbRow = table.insertRow();
        for(let j = 0; j < colmns; j++) {
            const tData = tbRow.insertCell();
            let input = document.createElement('INPUT');
            tData.appendChild(input);
        }
    }
    deleteButton.setAttribute("class","btn btn-danger");
    addColumn.setAttribute("class", "btn btn-primary");
    addRow.setAttribute("class","btn btn-primary");
    divBlock.setAttribute("id","table"+numofTables);
    console.log("This is the id of the table"+divBlock.id);
    deleteButton.addEventListener("onclick",deleteThing(divBlock.id));
    addRow.addEventListener("onclick", function() {
        appendRow("table"+numofTables);
    })
    addColumn.addEventListener("onclick", function() {
        appendColumn("table"+numofTables);
    })
    console.log(divBlock.id);
    
    elem.appendChild(divBlock);
    divBlock.appendChild(table);
    divBlock.appendChild(addRow);
    divBlock.appendChild(addColumn);
    // divBlock.addEventListener("onclick", deleteThing(divBlock.id));
    divBlock.appendChild(deleteButton);
    numofTables++;
}  

// append row to the HTML table
function appendRow(elemId) {
    var tbl = document.getElementById(elemId), // table reference
        row = tbl.insertRow(tbl.rows.length),      // append table row
        i;
    // insert table cells to the new row
    for (i = 0; i < tbl.rows[0].cells.length; i++) {
        createCell(row.insertCell(i), i, 'row');
    }
}
 
// create DIV element and append to the table cell
function createCell(cell, placeHolderText, style) {
    var div = document.createElement('div'), // create DIV element
        txt = document.createTextNode(placeHolderText), // create text node
        inpt = document.createElement('input');
    inpt.setAttribute('placeholder', placeHolderText);
    div.appendChild(inpt);                    // append text node to the DIV
    div.setAttribute('class', style);        // set DIV class attribute
    div.setAttribute('className', style);    // set DIV class attribute for IE (?!)
    cell.appendChild(div);                   // append DIV to the table cell
}
function appendColumn(elemId) {
    var tbl = document.getElementById(elemId), // table reference
        i;
    // open loop for each row and append cell
    for (i = 0; i < tbl.rows.length; i++) {
        createCell(tbl.rows[i].insertCell(tbl.rows[i].cells.length), i, 'col');
    }
}

function createSlider(elem){
    const divBlock = document.createElement("div");
    const title = document.createElement("input");
    const leftLabel = document.createElement("input");
    const slideDiv = document.createElement("div");
    const slider = document.createElement("input");
    const rightLabel = document.createElement("input");
    const deleteButton = document.createElement("button");

    title.setAttribute("id", "slidersTitle");
    leftLabel.setAttribute("class", "sliderLabel");
    leftLabel.setAttribute("id", "leftSliderRange");
    slideDiv.setAttribute("class", "slidecontainer");
    slider.setAttribute("type", "range");
    slider.setAttribute("id", "myRange");
    slider.setAttribute("class", "slider");
    slider.setAttribute("max", "100");
    slider.setAttribute("min", "1");
    rightLabel.setAttribute("class", "sliderLabel");
    rightLabel.setAttribute("id", "rightSliderRange");
    deleteButton.setAttribute("class","btn btn-danger");
    divBlock.setAttribute("id","slider"+numofSliders);
    console.log(divBlock.id);
    
    elem.appendChild(divBlock);
    divBlock.appendChild(title);
    divBlock.appendChild(leftLabel);
    divBlock.appendChild(slideDiv);
    divBlock.appendChild(slider);
    divBlock.appendChild(rightLabel);
    divBlock.appendChild(deleteButton);
    // deleteButton.addEventListener(onclick,deleteThing(divBlock.id));
    numofSliders++;
}

function deleteThing(id) {
    console.log(id);
    if (document.getElementById(id)) {
        document.getElementById(id).remove();
    }
    console.log(document.getElementById(id) + id);
}


let slider = document.getElementById("myRange");
let output = document.getElementById("demo");
// console.log(slider.value);
// console.log(document.getElementById("myRange").value);
// output.innerHTML = slider.value; // Display the default slider value

// Update the current slider value (each time you drag the slider handle)
slider.oninput = function() {
  output.innerHTML = this.value;
}
