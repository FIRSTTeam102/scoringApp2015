function startUp(){
	groupHighlights = [];
	// Creates a public variable called groupHighlights that contains the id value
	// of the current highlighted element in the group.
	// Example: Group 3 has element id #27 highlighted. [groupHighlights[3] === 27.]
}
startUp();
function highlightCheck(id, group, type, colorH, colorU){
	// highlightCheck accepts:
	// The html ID of the element (MANUALLY SPECIFIED IN THE FUNCTION CALL)
	// The group of the buttons (like a group of radio buttons, where only one can be selected at a time)
	// The type of selection (Image, Background, Text, Border)
	// The value of highlighted color
	// The value of unhighlighted color
	
	element = document.getElementById(id);
	// Sets a public variable to be saved as the currently selected element.
	// For use of the highlight call.
	// Reduces clutter, and might possibly reduce lag by like 0.1%
	
	if (groupHighlights[group] === undefined) {
		// Checks if there is NOT currently a highlighted element saved for that group.
			typeCheck(type, colorH, colorU, true);
			// Highlights the element. "True" used for isHighlighting variable used in typeCheck.
			groupHighlights[group] = id;
			// Saves the groupHighlights value for its group to the id of the selected element.
		} else {
			// If there IS currently a highlighted element saved...
			if (groupHighlights[group] === id) {
				// If the currently highlighted element is the one being selected...
				typeCheck(type, colorH, colorU, false);
				// Calls the highlight function with "False" for isHighlighting.
				groupHighlights[group] = undefined;
				// Set groupHighlights value to not exist
			} else {
				// If the currently highlighted element is NOT the one being selected... 
				typeCheck(type, colorH, colorU, true); 
				// Highlights the selected element
				element = document.getElementById(groupHighlights[group]);
				// Sets current element variable to currently selected groupHighlights 
				// Purpose being that directly below, we need to unhighlight the currently highlighted element.
				typeCheck(type, colorH, colorU, false);
				// yup.
				groupHighlights[group] = id;
				// Sets groupHighlights to selected element's id..
			}
		}
}

function typeCheck(type, colorH, colorU, isHighlighting){
	switch(type){
		// Creates a check for the type of highlighting that the element wants.
		// I made a simple highlight function for each 4 types of highlight. Isn't that nice? <3
		case 0:
			// 0 = image.
			setImageHighlight(element, colorH, colorU, isHighlighting);
			break;
		
		case 1:
			// 1 = background.
			setBackgroundHighlight(colorH, colorU, isHighlighting);
			break;
		
		case 2:
			// 2 = text.
			setTextHighlight(element, colorH, colorU, isHighlighting);
			break;
		
		case 3:
			// 3 = border.
			setBorderHighlight(element, colorH, colorU, isHighlighting);
	}
}

function setImageHighlight(element, colorH, colorU, isHighlighting){
	if(isHighlighting){
		// If I want to highlight...
		element.src = element.src.replace(colorU, colorH)
		// Changes the "src" attribute (url or filepath) of the element (being an image) to replace Unhighlighted word with Highlighted word.
		// i.e. (http://www.team102.com/2015/resources/images/tote) + (blue / red) in my example
	}else{
		// Otherwise...
		element.src = element.src.replace(colorH, colorU)'
		// Replaces highlighted keyword with unhighlighted keyword
	}
}

function setBackgroundHighlight(colorH, colorU, isHighlighting){
	if(isHighlighting){
		element.style.background = colorH;
		// Sets CSS style of the element's background to highlighted.
	}else{
		element.style.background = colorU;
		// Sets CSS style of the element's background to unhighlighted.
	}
}

function setTextHighlight(element, colorH, colorU, isHighlighting){
	if(isHighlighting){
		element.style.color = colorH;
		// Sets CSS style of the element's text to highlighted.
	}else{
		element.style.color = colorU;
		// Sets CSS style of the element's text to unhighlighted.
	}
}

function setBorderHighlight(element, colorH, colorU, isHighlighting){
	if(isHighlighting){
		element.style.borderColor = colorH;
		// CSS border highlighted
	}else{
		element.style.borderColor = colorU;
		// CSS border unhighlight
	}
}

