//	modal.js

/*	Modal image viewer and posting comments	*/

const modal = document.getElementById('modal_id');
const modalImg = document.getElementById("modal_image");

const next = document.getElementsByClassName('next')[0];
const prev = document.getElementsByClassName('prev')[0];

let span = document.getElementsByClassName("close")[0];
let downloadModalButton = document.getElementsByClassName('download_modal')[0];
let	picFrames = document.getElementsByClassName('pic');	

function bindModal(evnt) {
	
	modal.style.display = "flex";
	gallery.style.filter="blur(4px)";
	
	let list = [];
	for (let e of picFrames) {
		list.push(e);
	}
	
	let frame = evnt.currentTarget;
	let i = list.indexOf(frame);
		
	setImage(frame);
	getComments();
	
	downloadModalButton.onclick = function() {
		downloadFile();
	}
	next.onclick = function() {
		i += 1;
		if (i >  picFrames.length - 1)
			i = 0;
		
		setImage(picFrames[i]);
		getComments();
	}
	prev.onclick = function() {
		i -= 1;
		if (i < 0)
			i = picFrames.length - 1 ;
		
		setImage(picFrames[i]);
		getComments();
	}	
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() { 
		modal.style.display = "none";
		gallery.style.filter="";
				
	}

};

function setImage(frame) {
	let filename = frame.userData['thumb'];
	modal.currentFrame = frame;
		
	if(frame.modalImgUrl == null) {
	
		const modalImageRequest = new Request('src/modal.php?i='+filename, {
			method: 'GET',
			mode: 'cors',
			cache: 'default',
		});

		fetch(modalImageRequest)
			.then((response) => response.blob())
			.then((image) => {
			
				frame.modalImgUrl = URL.createObjectURL(image);
				modalImg.src = frame.modalImgUrl;
				
		  });
		  
	} else {
		modalImg.src = frame.modalImgUrl;
		
	}
}

function downloadFile() {
	let currentFrame = modal.currentFrame;
	let filename = currentFrame.userData['thumb'];
	let originalName = currentFrame.userData['name'];
	
	const fileRequest = new Request('src/single.php?i='+filename, {
		method: 'GET',
		mode: 'cors',
		cache: 'default',
	});

	fetch(fileRequest)
		.then((response) => response.blob())
		.then((file) => {
			
			let link = document.createElement("a");
			link.style.display = 'none';
			document.body.appendChild(link);
			link.href = URL.createObjectURL(file);
			link.download = originalName;
			link.click();
			//	Free memory after 10 mins
			setTimeout(function() {
				URL.revokeObjectURL(link.href);
				link.remove(); 
			}, 600000);
				
	  });
		
}


var modalComment = document.getElementById('comments');
var commentForm = document.getElementById('commentForm');
commentForm.addEventListener('submit', sendComment);


function sendComment(evnt) {

	evnt.preventDefault();
	let thumbName = modal.currentFrame.userData['thumb'];
	
	let formData = new FormData(commentForm);
	formData.append('thumb', thumbName); 
	
	
	const modalCommentsRequest = new Request('src/comment.php', {
		method: 'POST',
		body: formData,
		mode: 'cors',  
		cache: 'default'
	});

	fetch(modalCommentsRequest)
		.then((response) => response.json())
		.then((records) => {
			getComments();
			
	  });

}


function getComments() {
	let filename = modal.currentFrame.userData['thumb'];
	/*	Clear */
	while (modalComment.firstChild) {
		modalComment.removeChild(modalComment.firstChild);
	}
	
	let formData = new FormData();
	formData.append('thumb', filename); 
	
	const modalCommentsRequest = new Request('src/comment.php', {
		method: 'POST',
		body: formData,
		mode: 'cors',  
		cache: 'default'
	});

	fetch(modalCommentsRequest)
		.then((response) => response.json())
		.then((records) => {
			
			
			
			for (let rec of records) {
			
				let user = document.createElement('span');
				user.innerText = rec['author'];
				user.setAttribute('class', 'modal_comment_author');
				
				let date = document.createElement('span');
				date.innerText = rec['date'];
				date.setAttribute('class', 'modal_comment_date');
				
				let text = document.createElement('div');
				text.innerText = rec['text'];
				text.setAttribute('class', 'modal_comment_text');
				
				let c = document.createElement('div');
				c.appendChild(user);
				c.appendChild(date);
				c.appendChild(text);
	
				modalComment.append(c);
			
			}
			
	  });

}

