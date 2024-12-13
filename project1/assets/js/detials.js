function refreshComments(video_id){
    fetch("assets/api/functions.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            function: "getComments",
            videoId: video_id
        })
    })
    .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
    .then(data => { 
        document.querySelector('.comments-list').innerHTML = '';
        
        for (let i = 0; i < data.length; i++) {
            let comment = data[i];
            let commentDiv = document.createElement('div');
            commentDiv.classList.add('comment');
            let pNameDate = document.createElement('p');
            let strongTag = document.createElement('strong');
            pNameDate.appendChild(strongTag);
            pNameDate.innerHTML += comment.name + '<span class="time">' + comment.time_ago + '</span>';
            commentDiv.appendChild(pNameDate);
            let pMessage = document.createElement('p');
            pMessage.textContent = comment.message;
            commentDiv.appendChild(pMessage);
            document.querySelector('.comments-list').appendChild(commentDiv);
        }
        
    })
    .catch(err => {
        console.error("Error fetching events data:", err);
    });
}

document.querySelector('.addCommentForm').addEventListener('submit', (e) => {
    event.preventDefault();

    fetch("assets/api/functions.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            function: "setComment",
            video_id: movieId,
            name: document.querySelector('.addCommentForm input[name="name"]').value,
            email: document.querySelector('.addCommentForm input[name="email"]').value,
            message: document.querySelector('.addCommentForm textarea[name="message"]').value
        })
    })
    .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
    .then(data => { 
        if(data.succes == 'Reaction save succesfully'){
            refreshComments(movieId);
        }
    })
    .catch(err => {
        console.error("Error fetching events data:", err);
    });
});

refreshComments(movieId);