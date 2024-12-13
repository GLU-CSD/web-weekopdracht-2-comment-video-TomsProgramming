function login(){
    window.location.href = './login.php';
}

function getComments(videoId){
    fetch("assets/api/functions.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            function: "getComments",
            video_id: videoId
        })
    })
    .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
    .then(data => { 
        if(data.status == "success") {
            setComments(data.comments);
        }
    })
    .catch(err => {
        console.error("Error fetching events data:", err);
    });
}

function setComments(data){
    let commentsDiv = document.querySelector('.cmnts');
    commentsDiv.innerHTML = '';

    data.forEach(comment => {
        let commentDiv = document.createElement('div');
        commentDiv.classList.add('comment');
        commentDiv.innerHTML = `
            <div class="old-cmnt">
                <img src="${comment.profilePicturePath}" alt="" srcset="">
                <div>
                    <h3>
                        ${comment.username} <span>${comment.time_ago}</span>
                    </h3>
                    <p>${comment.message}</p>
                </div>
            </div>
        `;

        commentsDiv.appendChild(commentDiv);
    });
}

function getVideoHistory(videoId, videoElement){
    fetch("assets/api/functions.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            function: "getVideoHistory",
            video_id: videoId
        })
    })
    .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
    .then(data => { 
        console.log(data);
        if(data.status == "success") {
            videoElement.currentTime = data.last_position;
        }
    })
}

window.addEventListener('DOMContentLoaded', (event) => {
    let menuIcon = document.querySelector(".menu-icon");
    let sidebar = document.querySelector(".sidebar");
    let container = document.querySelector(".container");

    menuIcon.onclick = function(){
        sidebar.classList.toggle("small-sidebar");
        container.classList.toggle("large-container");    
    }

    document.querySelector('.upload-video').addEventListener('click', () => {
        document.querySelector('.upload').style.display = 'flex';
    });

    document.querySelector('.upload-box .back').addEventListener('click', () => {
        document.querySelector('.upload').style.display = 'none';
    });

    document.querySelector('.nav-left .logo').addEventListener('click', () => {
        window.location.href = './index.php';
    });

    document.querySelector('.uploadForm').addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('function', 'uploadVideo');
        formData.append('video', document.querySelector('.uploadForm #video').files[0]);
        formData.append('title', document.querySelector('.uploadForm #title').value);
        formData.append('description', document.querySelector('.uploadForm #description').value);
        formData.append('thumbnail', document.querySelector('.uploadForm #thumbnail').files[0]);

        fetch("assets/api/functions.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
        .then(data => { 
            if(data.status == "success") {
                window.location.href = './index.php';
            }
        })
        .catch(err => {
            console.error("Error fetching events data:", err);
        });
    });


    const commentsDiv = document.querySelector('.cmnts');
    const addCommentForm = document.querySelector('.add-cmnt');
    let videoId = null;
    if(commentsDiv){
        videoId = commentsDiv.getAttribute('data-video-id');
        getComments(videoId);
    }

    if(addCommentForm && videoId){
        addCommentForm.addEventListener('submit', (e) => {
            e.preventDefault();
        
            const message = document.querySelector('.add-cmnt #message').value;
            fetch("assets/api/functions.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    function: "addComment",
                    video_id: videoId,
                    message: message
                })
            })
            .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
            .then(data => { 
                if(data.status == "success") {
                    getComments(videoId);
                }
            })
        });
    }

    const videoElement = document.querySelector('.video');
    if(videoElement && videoId){
        getVideoHistory(videoId, videoElement);

        setInterval(() => {
            const currentTime = Math.floor(videoElement.currentTime);
            
            fetch("assets/api/functions.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    function: "updateVideoHistory",
                    video_id: videoId,
                    current_time: currentTime
                })
            })
        
        }, 5000);
    }
});