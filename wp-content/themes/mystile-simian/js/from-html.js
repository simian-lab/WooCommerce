var printPdfHtml = function(){
var doc = new jsPDF();

var specialElementHandlers = {
	'#no-print-pdf': function(element, renderer){
		return true;
	}
};

// All units are in the set measurement for the document
// This can be changed to "pt" (points), "mm" (Default), "cm", "in"
doc.fromHTML(jQuery('#content').get(0), 15, 15, {
	'width': 170, 
	'elementHandlers': specialElementHandlers
});

doc.save('ReciboPDF.pdf');
}