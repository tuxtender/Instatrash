//	main.js

let button = document.getElementById('authenticate');
let gallery = document.getElementById('gallery');
let menu = document.getElementById('menu');
var form = document.forms["authentication"];

var currentDir = '/';
var userInfo;
const THUMBNAILS = "thumbnails/";
let selected = [];

button.addEventListener('click', logging);
button.click();


function logging() {
	var formData = new FormData(form);
		
	var myInit = { 	method: 'POST',
					body: formData,  
					mode: 'cors',
					cache: 'default' };

	var dataRequest = new Request( 'src/login.php', myInit);
      
	fetch(dataRequest)				
		.then(function(response) {
			if (response.status == 200) {
				return response.json();
				
			} else {
				form.style.display = 'block';
				throw new Error("HTTP status " + response.status);
			}
		
		}).then(function(json) {
		
			userInfo = json['user'];
			makeNavBar('/');
			form.remove();
			menu.style.display = 'block';
			gallery.style.display = 'block';
			resizeGalleryWallpaper();

			menu.userRefLink = '/';
			menu.addEventListener('click', getUserData);
			menu.dispatchEvent(new Event('click'));
			menu.removeEventListener('click', getUserData);
			
			
		});
		
     
};



function getUserData(e) {
	let path = e.currentTarget.userRefLink;
	var formData = new FormData();
	formData.append('path', path);
		
	makeNavBar(path);
		
	while (gallery.firstChild) {
		gallery.removeChild(gallery.firstChild);
	}
	
	var myInit = { 	method: 'POST',
					body: formData,
					mode: 'cors',
					cache: 'default' };

	var dataRequest = new Request( 'src/data.php', myInit);

      
	fetch(dataRequest)				//Get user data from server
		.then(function(response) {
			return response.json()
		})
		.then(function(json) {		//Preview files after access
			//userInfo = json['user'];
			showFolder(json['folders']);
			showImage(json['files']);	
			resizeGalleryWallpaper();
			
		});
     
};


/*	Create new a document element for manipulations files server	*/
/*	(view, order, save, load)  										*/

function showImage(data) {

	for (let pic of data) {
		let div = document.createElement('div');
		div.isDirectory = false;
		div.userData = pic; //Put server data of each element to div.frame
		div.modalImgUrl;	//Undefined image for Modal
		div.setAttribute('class', 'pic responsive thumbnail');
			
		/*	A thumbnail image of div's container	*/
		let img = document.createElement('img');
		img.src = THUMBNAILS + pic['thumb'];
		img.alt = pic['name'];
		img.setAttribute('class', 'image ');
		
		//img.appendChild(checkBox);
		/*	Create a check box on image*/
		let checkBox = document.createElement('div');
		checkBox.setAttribute('class', 'check_box');
		
		let checker = document.createElement('img');
		checker.setAttribute('class', 'checker');
		checker.src = "images/accept.svg";
		
		checkBox.appendChild(checker);
			
		let name = document.createElement('div');
		name.setAttribute('class', 'frame_name');
		name.innerText = pic['name'];
			
		div.appendChild(img);
		div.appendChild(checkBox);	
		
		div.appendChild(name);
				
		gallery.appendChild(div);
		
		div.addEventListener('click', bindModal);
		
		
	}
};

function showFolder(data) {
	
	for (let dir of data) {
		let div = document.createElement('div');
		div.isDirectory = true;
		
		div.userData = dir;
		div.userRefLink = dir['path'] + dir['folder_name'] + '/';
		div.setAttribute('class', 'responsive thumbnail');
			
		/*	A thumbnail image of div's container	*/
		let img = document.createElement('img');
		img.src = 'images/folder.png';
		img.alt = dir['folder_name'];
		img.setAttribute('class', 'image ');
		
		/*	Create a check box on image*/
		let checkBox = document.createElement('div');
		checkBox.setAttribute('class', 'check_box');
		
		let checker = document.createElement('img');
		checker.setAttribute('class', 'checker');
		checker.src = "images/accept.svg";
		
		checkBox.appendChild(checker);
				
		let name = document.createElement('div');
		name.setAttribute('class', 'frame_name');
		name.innerText = dir['folder_name'];
			
		div.appendChild(img);
		div.appendChild(checkBox);
		div.appendChild(name);
		gallery.appendChild(div);
		
		div.addEventListener('click', getUserData );
	
	}

};

window.addEventListener("resize", resizeGalleryWallpaper);

function resizeGalleryWallpaper() {
	let y = gallery.getBoundingClientRect()["top"];
	let maxY = window.innerHeight;
	let minHeight = maxY - (Number(y)).toFixed() ;
	
	gallery.style.minHeight = minHeight +"px";
}


var navBar = document.getElementById('nav_bar');

function makeNavBar(path) {
	currentDir = path;
	
	while (navBar.firstChild) {
		navBar.removeChild(navBar.firstChild);
	}
	
	let e = document.createElement('span');

	e.userRefLink = '/';
	e.innerText = userInfo['nickname'];

	e.addEventListener('click', getUserData );
	e.setAttribute('class', 'navbar_dir');
	navBar.append(e);
	
	
	let list = path.split('/');
	let p = '/';
	
	for (let i = 1; i < list.length-1; i++) {
		p += list[i] + '/';

		let e = document.createElement('span');
		e.userRefLink = p;
		e.innerText = list[i];
		
		let d = document.createElement('span');
		d.innerText = '/';
		navBar.append(d);
		
		e.addEventListener('click', getUserData );
		e.setAttribute('class', 'navbar_dir');
		navBar.append(e);
	}
	
}
