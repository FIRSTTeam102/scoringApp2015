startUp();
var groupHighlights = new Array(4);
var greyStart, greyEnd;
var yellowStart, yellowEnd;
var identity;
var gearheads;
var colorDark, colorDull, color;

function startUp(){
	createGrey();
	createYellow();
		
		if(document.getElementById("mBlue")===null){
			colorDark = "#DE1409";
			colorDull = "#FA9898";
			color = "red";
		}else{
			colorDark = "#164DC4";
			colorDull= "#9AB9FC";
			color = "blue";
		}
		for(var i=0; i<3; i++){
			if(document.getElementById(39+i).innerHTML==="102"){
				setBackgroundHighlight(39+i, 0, "#FCC160");
			    setTextHighlight(39+i, "#F07802")
				gearheads = (39+i);
			}
			setBorderHighlightById(39+i, false);
		}
		setBorderHighlightBySelector("score",0, colorDark);
		setBorderHighlightBySelector("score",1, colorDark);
		setBorderHighlightBySelector("score",2, colorDark);
		setBorderHighlightBySelector("score",3, colorDark);
		setBackgroundHighlight("score0", undefined, colorDull);
		setBackgroundHighlight("score1", undefined, colorDull);
		setBackgroundHighlight("score2", undefined, colorDull);
		setBackgroundHighlight("score3", undefined, colorDull);
		setBorderHighlightById("Next", false);
		setBorderHighlightById("Done", false);
}

function createGrey() {
    for (var i = 0; i < 6; i++) {
      
        for (var j = 0; j < 6; j++) {
          
            var id = i * 6 + j;
          
            if (i + j >= 6) {
              
                document.getElementById("row" + i).innerHTML = document.getElementById("row" + i).innerHTML + "<td id='" + id + "' class='tableD'> <img class='totes' src='http://www.team102.org/2015/resources/images/totegrey.png'</td>";
              
            } else {
              
                document.getElementById("row" + i).innerHTML = document.getElementById("row" + i).innerHTML + "<td id='" + id + "' class='tableD'> <img class='totes' src='http://www.team102.org/2015/resources/images/totedull.png' onclick='changeImage(" + id + ",0)'/></td>";

            }
        }
    }
}

function createYellow() {
    for (var i = 0; i < 4; i++) {
      
        for (var j = 0; j < 4; j++) {
          
            var id = (i * 4 + j);
			
			
            if (i + j >= 4 || (i===0&&j===0)) {
              
                document.getElementById("yellow" + i).innerHTML = document.getElementById("yellow" + i).innerHTML + "<td id='" + (id+100)+ "';> <img class='totes'  class='tableYD' src='http://www.team102.org/2015/resources/images/yellowtotegrey.png'</td>";
              
            } else {
              
                document.getElementById("yellow" + i).innerHTML = document.getElementById("yellow" + i).innerHTML + "<td id='" + (id+100)+ "';> <img class='totes' class='tableYD' src='http://www.team102.org/2015/resources/images/yellowtotedull.png' onclick='changeImage(" + (id+100) + ",3)'/></td>";

            }
        }
    }
}

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
    if (groupHighlights[0]) {
      
        startingHeight = id % 6;
        endingHeight = 6 - Math.floor(id / 6);
      
    } else {
      
        startingHeight = undefined;
        endingHeight = undefined;
      
    }
  
}

function highlightCheck(id, group, isHighlighting) {
  
    var column, row;
    switch (group) {
        case 0:
			column = id%6;
			row = 6-Math.floor(id/6);
				if(isHighlighting){
					setInputValue(group, column, "s");
					setInputValue(group, row, "e");
            	}else{
					setInputValue(group, "", "s");
					setInputValue(group, "", "e");
				}
			
            lowerHighlight(id, group, isHighlighting, 6, 0);
            break;
        
        case 1:
			
            if (isHighlighting) {
              
                setHighlight(id);
                if(id===37){
                    setInputValue(1,1,"c");
                }else{
                    setInputValue(1,1,"l");
                }
				
            } else {
              
                removeHighlight(id);
                if(id===37){
                    setInputValue(1,"","c");
                }else{
                    setInputValue(1,"","l");
                }

            }
            break;
        
        case 2:
            if (isHighlighting) {
				
				if(id===gearheads){
					setBackgroundHighlight(id, group, "#F07802");
					setBorderHighlightById(id,true);
					setTextHighlight(id, "#FCC160");
				}else{
					setBackgroundHighlight(id, group, "#383838");
					setBorderHighlightById(id,true);
					setTextHighlight(id, "#F5F5F5");
					
				}
                
                setInputValue(2, (id-38), "");
				
            } else if(!isHighlighting) {
				
				if(id===gearheads){
					setBackgroundHighlight(id, group, "#FCC160");
					setBorderHighlightById(id,false);
					setTextHighlight(id, "#F07802");
				}else{
					setBackgroundHighlight(id, group, "#F5F5F5");
					setBorderHighlightById(id,false);
					setTextHighlight(id, "#383838");
				}
                
                setInputValue(2, "", "");
                
            }
            break;
		case 3:
			column = id%4;
			row = 4-Math.floor((id-100)/4);
				if(isHighlighting){
					setInputValue(group, column, "s");
					setInputValue(group, row, "e");
            	}else{
					setInputValue(group, "", "s");
					setInputValue(group, "", "e");
				}
		
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
          
        } else if (isHighlighting) {
			if(modifier===4){
				setHighlight((id+x), group);
			}else{
				setColoredHighlight((id+x), group);
			}
            row++;
            id = row * modifier + column;
          
        } else {
			if(modifier===4){
				removeHighlight((id+x), group);
			}else{
				removeColoredHighlight((id+x), group);
			}
			
            row++;
            id = row * modifier + column;
          
        }
      
    } while (!end);
  
}

function removeHighlight(id, group) {
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace("selected", "dull");
  
}

function removeColoredHighlight(id, group) {
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace(color, "dull");
  
}

function setHighlight(id, group) {
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace("dull", "selected");
  
}

function setColoredHighlight(id, group) {
  
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML.replace("dull", color);
  
}

function setBackgroundHighlight(id, group, color) {
  
    document.getElementById(id).style.background = color;
  
}

function setTextHighlight(id, color){
	document.getElementById(id).style.color=color;
}

function setBorderHighlight(selector){
  document.querySelectorAll(selector)[0].style.borderColor = colorDark;
}

function setBorderHighlightBySelector(tag,selector){

  document.getElementById(tag+selector).style.borderColor = colorDark;
}

function setBorderHighlightById(id, light){
	if(light){
		
		document.getElementById(id).style.borderColor = colorDull;		
	
	}else{
		
		document.getElementById(id).style.borderColor = colorDark;		
	
	}
}

function setInputValue(group, value, extension){
  document.getElementById("input"+group+extension).value=value;
}