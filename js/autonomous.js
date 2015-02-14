var groupHighlights = new Array(23);

startUp();
function startUp(){
  var color;
  for(var i=0; i<3; i++){
    if(document.getElementsByClassName("team")[i].innerHTML==="102"){
      document.getElementsByClassName("team")[i].style.background = "#ff7e00";
    }
    if(document.getElementById("allianceColor").innerHTML==="RED"){
      color = "DE1409";
      setBorderHighlight(i, ".teamABC", color);
    }else{
      color = "#164DC4";
      setBorderHighlight(i, ".teamABC", color);
    }
  }
  setBorderHighlight("0", ".Alliance", color);
  setBorderHighlight("0", ".match", color); 
  setBorderHighlight("0", ".title", color);
  setBorderHighlight("0", ".bigButton", color);
}

  
function changeImage(id, group, parent, child) {
  
  if(groupHighlights[child]!==undefined){
    highlightCheck(groupHighlights[child], group, false);
    groupHighlights[child]=undefined;
  }
  
    if (groupHighlights[group] === undefined) {
      if(parent!==undefined){
        changeImage(parent,(group));
        groupHighlights[group-1]=parent;
      }
      
        groupHighlights[group] = id;
        highlightCheck(id, group, true);
    } else {
        if (groupHighlights[group] === id) {
          if(parent!==undefined){
            highlightCheck(parent, (group-1), false);
            groupHighlights[group-1] = undefined;
          }
            highlightCheck(id, group, false);
            groupHighlights[group] = undefined;
        } else {
          if(parent!==undefined){
            highlightCheck(parent,(group-1), true);
          }
            highlightCheck(groupHighlights[group], group, false);
            groupHighlights[group] = id;
            highlightCheck(id, group, true);
        }
    }
  } 


function highlightCheck(id, group, isHighlighting) {
  if(isHighlighting===true){
    setTextHighlight(id, "#f7b448", "#635415");
  }else{
    setTextHighlight(id, "#21211E", "white");
  }
}

function setTextHighlight(id, color, text) {
    document.getElementById(id).style.background = color;
  if(text !== undefined){
    document.getElementById(id).style.color = text;
  }
}
function setBorderHighlight(i, selector, color){
  document.querySelectorAll(selector)[i].style.borderColor = color;
}
function cleanUp(){
  var x;
  for(var i=0; i<groupHighlights.length; i++){
    x=document.getElementById("data").innerHTML;
  document.getElementById("data").innerHTML = x+groupHighlights[i]+", ";
  }
  document.getElementById("truemain").innerHTML = "";
}



