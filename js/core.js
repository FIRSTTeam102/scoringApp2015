createGrey();

function createGrey() {
    var createCounter = 0;
    for (var i = 0; i < 6; i++) {
      
        for (var j = 0; j < 6; j++) {
          
            var identity = i * 6 + j;
          
            if (i + j >= 6) {
              
                document.getElementById("row" + i).innerHTML = document.getElementById("row" + i).innerHTML + "<td id='" + identity + "';> <img class='totes' src='http://www.team102.org/2015/resources/images/totelight.png'</td>";
              
            } else {
              
                document.getElementById("row" + i).innerHTML = document.getElementById("row" + i).innerHTML + "<td id='" + identity + "';> <img class='totes' src='http://www.team102.org/2015/resources/images/totegrey.png' onclick='changeImage(" + identity + ",0)'/></td>";

            }
        }
    }
}

var groupHighlights = new Array(3), startingHeight, endingHeight;

function changeImage(id, group) {
  
    if (groupHighlights[group] === undefined) {
      
        groupHighlights[group] = id;
        highlightCheck(id, group, true);
      
    } else {
      
        if (groupHighlights[group] === id) {
          
            highlightCheck(id, group, false);
            groupHighlights[group] = undefined;
          
        } else {
          
            highlightCheck(groupHighlights[group], group, false);
            groupHighlights[group] = id;
            highlightCheck(id, group, true);
          
        }
    }
    if (groupHighlights[0] !== undefined) {
      
        startingHeight = id % 6;
        endingHeight = 6 - Math.floor(id / 6);
      
    } else {
      
        startingHeight = undefined;
        endingHeight = undefined;
      
    }
  
    document.getElementById("paragraph").innerHTML = "groupHighlights: (" + groupHighlights[0] + ", " + groupHighlights[1] + ", " + groupHighlights[2] + ")<br>selected: (" + startingHeight + ", " + endingHeight + ")";
  
}

function removeHighlight(id, group) {
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace("dark", "grey");
  
}

function setHighlight(id, group) {
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace("grey", "dark");
  
}

function highlightCheck(id, group, isHighlighting) {
  
    switch (group) {
        case 0:
        
            lowerHighlight(id, group, isHighlighting);
            break;
        
        case 1:
        
            if (isHighlighting === true) {
              
                setHighlight(id);
              
            } else {
              
                removeHighlight(id);

            }
            break;
        
        case 2:
            if (isHighlighting === true) {
                setTextHighlight(id, group, "#8a8a8a");

            } else {
                setTextHighlight(id, group, "#3b444b");
            }
            break;
    }
}

function lowerHighlight(id, group, isHighlighting) {
  
    var row = Math.floor(id / 6),
        column = id % 6,
        end = false;
    do {
      
        if (row + column >= 6) {
          
            end = true;
          
        } else if (isHighlighting === true) {
          
            setHighlight(id, group);
            row++;
            id = row * 6 + column;
          
        } else {
          
            removeHighlight(id, group);
            row++;
            id = row * 6 + column;
          
        }
      
    } while (end === false);
  
}

function setTextHighlight(id, group, color) {
  
    document.getElementById(id).style.background = color;
  
}