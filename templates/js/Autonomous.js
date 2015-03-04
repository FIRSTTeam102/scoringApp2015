var groupHighlights = new Array(23);
startUp();
function startUp() {
	var color;
	for (var i = 0; i < 3; i++) {
		if (document.getElementsByClassName("team")[i].innerHTML === "102") {
			document.getElementsByClassName("team")[i].style.background = "#ff7e00";
		}
		if(document.getElementById("mBlue")===null){
			colorDark = "#DE1409";
		}else{
			colorDark = "#164DC4";
		}
	}
	setBorderHighlightById("team0", colorDark);
	setBorderHighlightById("team1", colorDark);
	setBorderHighlightById("team2", colorDark);
	setBorderHighlightById("Next", colorDark);
}


function changeImage(id, group, parent, child, childValue) {
	if (child !== undefined) {
		changeImage((id + 1), child);
	}
	if (groupHighlights[child] !== undefined) {

		highlightCheck(groupHighlights[child], group, false);
		groupHighlights[child] = undefined;
		setInputValue(child, "");

	}

	if (groupHighlights[group] === undefined) {
		if (parent !== undefined) {
			changeImage(parent, (group));
			groupHighlights[group - 1] = parent;

		}

		groupHighlights[group] = id;
		highlightCheck(id, group, true);

		if (childValue !== undefined) {

			setInputValue(group, childValue);

		} else {

			setInputValue(group, 1);

		}
	} else {
		if (groupHighlights[group] === id) {

			if (parent !== undefined) {

				highlightCheck(parent, (group - 1), false);
				groupHighlights[group - 1] = undefined;

			}
			highlightCheck(id, group, false);
			groupHighlights[group] = undefined;
			setInputValue(group, "");

		} else {
			if (parent !== undefined) {
				highlightCheck(parent, (group - 1), true);

			}
			highlightCheck(groupHighlights[group], group, false);

			groupHighlights[group] = id;
			highlightCheck(id, group, true);
			if (childValue !== undefined) {
				setInputValue(group, childValue);
			} else {
				setInputValue(group, 1);
			}

		}
	}
}

function setInputValue(name, value) {
	document.getElementById("input" + name).value = value;
}

function highlightCheck(id, group, isHighlighting) {
	if (isHighlighting === true) {
		setTextHighlight(id, "#f7b448", "#635415");
	} else {
		setTextHighlight(id, "#21211E", "white");
	}
}

function setTextHighlight(id, color, text) {

	document.getElementById(id).style.background = color;
	if (text !== undefined) {
		document.getElementById(id).style.color = text;
	}
}

function setBorderHighlightById(id, color) {
	document.getElementById(id).style.borderColor = color;
}