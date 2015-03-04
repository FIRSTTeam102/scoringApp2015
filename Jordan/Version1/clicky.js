function startUp(){
	groupHighlights = [];
}
startUp();
function highlightCheck(id, group, type, colorH, colorU){
	
	currentHighlighted = groupHighlights[group];
	element = document.getElementById(id);
	
	if (groupHighlights[group] === undefined) {
			groupHighlights[group] = id;
			typeCheck(type, colorH, colorU, true);
		} else {
			if (groupHighlights[group] === id) {
				groupHighlights[group] = undefined;
				typeCheck(type, colorH, colorU, false);
			} else {
				typeCheck(type, colorH, colorU, true);
				element = document.getElementById(groupHighlights[group]);
				typeCheck(type, colorH, colorU, false);
				groupHighlights[group] = id;
			}
		}
}

function typeCheck(type, colorH, colorU, isHighlighting){
	switch(type){
		case 0:
			setImageHighlight(element, colorH, colorU, isHighlighting);
			break;
		
		case 1:
			setBackgroundHighlight(colorH, colorU, isHighlighting);
			break;
		
		case 2:
			setTextHighlight(element, colorH, colorU, isHighlighting);
			break;
		
		case 3:
			setBorderHighlight(element, colorH, colorU, isHighlighting);
	}
}

function setImageHighlight(element, colorH, colorU, isHighlighting){
	if(isHighlighting){
		element.src = element.src.replace(colorU, colorH)
		
	}else{
		element.src = element.src.replace(colorH, colorU)
	}
}

function setBackgroundHighlight(colorH, colorU, isHighlighting){
	if(isHighlighting){
		element.style.background = colorH;
	}else{
		element.style.background = colorU;
	}
}

function setTextHighlight(element, colorH, colorU, isHighlighting){
	if(isHighlighting){
		element.style.color = colorH;
	}else{
		element.style.color = colorU;
	}
}

function setBorderHighlight(element, colorH, colorU, isHighlighting){
	if(isHighlighting){
		element.style.borderColor = colorH;
	}else{
		element.style.borderColor = colorU;
	}
}

