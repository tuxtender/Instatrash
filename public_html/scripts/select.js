//	select.js

function selectModeOff() {
	
	let frames = document.getElementsByClassName('responsive');
	
	for (let frame of frames) {
		frame.removeEventListener('click', selectImages);
		frame.style.opacity='';
		let checkBox = frame.getElementsByClassName('check_box')[0];
		checkBox.style.display = 'none';
		//checkBox.innerHTML = '';
		selected = [];
		if (frame.isDirectory) 
			frame.addEventListener('click', getUserData );
		else
			frame.addEventListener("click", bindModal); 
		
	}
	navBarOn();
}

function selectModeOn() {

	let frames = document.getElementsByClassName('responsive');
	
	for (let frame of frames) {
	
		if (frame.isDirectory) 
			frame.removeEventListener('click', getUserData );
		else
			frame.removeEventListener("click", bindModal); 
			
		frame.addEventListener('click', selectImages);
		frame.style.opacity="0.7";
		//let checkBox = frame.getElementsByClassName('check_box')[0];
		//checkBox.style.display = 'block';
		//checkBox.style.display ='flex';

		
		
	}
	
	navBarOff();
		
}


/*	Refactor with 'toggle'	and svg icon*/
function selectImages1(event) {
	/*	Filling a selected array*/
	let frame = event.currentTarget;
	let checkBox = frame.getElementsByClassName('check_box')[0];
		
	if (checkBox.innerHTML) {
		checkBox.innerHTML="";
				
		const index = selected.indexOf(checkBox.parentNode);
		if (index > -1) {
			selected.splice(index, 1);
		}
				
	} else {
		checkBox.innerHTML="&times;"
		/*	Parent's div add to a select list	*/
		selected.push(checkBox.parentNode);
	}
		
	
};

function selectImages(event) {
	/*	Filling a selected array*/
	let frame = event.currentTarget;
	let checkBox = frame.getElementsByClassName('check_box')[0];
		
	if (checkBox.style.display == 'block') {
		checkBox.style.display = 'none';
				
		const index = selected.indexOf(checkBox.parentNode);
		if (index > -1) {
			selected.splice(index, 1);
		}
				
	} else {
		checkBox.style.display ='block';
		/*	Parent's div add to a select list	*/
		selected.push(checkBox.parentNode);
	}
		
	
};


function navBarOff() {
	let dirs = navBar.getElementsByClassName('navbar_dir');
	for (let e of dirs) {
		e.removeEventListener('click', getUserData );
	}
}

function navBarOn() {
	let dirs = navBar.getElementsByClassName('navbar_dir');
	for (let e of dirs) {
		e.addEventListener('click', getUserData );
	}
}
