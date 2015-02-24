startUp();
function startUp() {
	createGrey();
	createYellow();
    var color;
    for (var i = 0; i < 3; i++){
        if (document.getElementById("allianceColor").innerHTML === "RED") {
            color = "DE1409";
            setBorderHighlight(i, ".score", color)
        } else {
            color = "#164DC4";
            setBorderHighlight(i, ".score", color)
        }
    }
    setBorderHighlight("0", ".Alliance", a);
    setBorderHighlight("0", ".match", a);
    setBorderHighlight("0", ".title", a);
    setBorderHighlight("0", ".bigButton", a)
}
function createGrey() {
    for (var i = 0; i < 6; i++) {
      
        for (var j = 0; j < 6; j++) {
          
            var identity = i * 6 + j;
          
            if (i + j >= 6) {
              
                document.getElementById("row" + i).innerHTML = document.getElementById("row" + i).innerHTML + "<td id='" + identity + "';> <img class='totes' src='http://www.team102.org/2015/resources/images/totegrey.png'</td>";
              
            } else {
              
                document.getElementById("row" + i).innerHTML = document.getElementById("row" + i).innerHTML + "<td id='" + identity + "';> <img class='totes' src='http://www.team102.org/2015/resources/images/totelight.png' onclick='changeImage(" + identity + ",0)'/></td>";

            }
        }
    }
}
function createYellow() {
    for (var i = 0; i < 4; i++) {
      
        for (var j = 0; j < 4; j++) {
          
            var identity = (i * 4 + j);
          
            if (i + j >= 4) {
              
                document.getElementById("yellow" + i).innerHTML = document.getElementById("yellow" + i).innerHTML + "<td id='" + (identity+100)+ "';> <img class='totes' src='http://www.team102.org/2015/resources/images/yellowtotegrey.png'</td>";
              
            } else {
              
                document.getElementById("yellow" + i).innerHTML = document.getElementById("yellow" + i).innerHTML + "<td id='" + (identity+100)+ "';> <img class='totes' src='http://www.team102.org/2015/resources/images/yellowtotelight.png' onclick='changeImage(" + (identity+100) + ",3)'/></td>";

            }
        }
    }
}

var groupHighlights = new Array(4), greyStart, greyEnd, yellowStart, yellowEnd;

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
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace("dark", "light");
  
}

function setHighlight(id, group) {
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace("light", "dark");
  
}

function highlightCheck(id, group, isHighlighting) {
  
    switch (group) {
        case 0:
        
            lowerHighlight(id, group, isHighlighting, 6, 0);
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
		case 3:
		
			lowerHighlight(id, group, isHighlighting, 4, 100);
			break;
    }
}

function lowerHighlight(id, group, isHighlighting, modifier, x) {
	id = id-x;
    var row = Math.floor(id / modifier),
        column = id % modifier,
        end = false;
    do {
      
        if (row + column >= modifier) {
          
            end = true;
          
        } else if (isHighlighting === true) {
          
            setHighlight((id+x), group);
            row++;
            id = row * modifier + column;
          
        } else {
          
            removeHighlight((id+x), group);
            row++;
            id = row * modifier + column;
          
        }
      
    } while (end === false);
  
}

function setTextHighlight(id, group, color) {
  
    document.getElementById(id).style.background = color;
  
}
function setBorderHighlight(identifier, name, color) {
    document.querySelectorAll(name)[identifier].style.borderColor = color;
}