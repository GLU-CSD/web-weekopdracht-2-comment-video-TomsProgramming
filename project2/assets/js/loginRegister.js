window.addEventListener('DOMContentLoaded', (event) => {
    if(document.querySelector(".registerForm")){
        document.querySelector(".registerForm").addEventListener('submit', (e) => {
            e.preventDefault();
        
            fetch("assets/api/functions.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    function: "register",
                    username: document.querySelector(".registerForm #username").value,
                    email: document.querySelector(".registerForm #email").value,
                    password: document.querySelector(".registerForm #password").value,
                    confirm_password: document.querySelector(".registerForm #confirm_password").value
                })
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
    }
    
    if(document.querySelector(".loginForm")){
        document.querySelector(".loginForm").addEventListener('submit', (e) => {
            e.preventDefault();
        
            fetch("assets/api/functions.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    function: "login",
                    username: document.querySelector(".loginForm #username").value,
                    password: document.querySelector(".loginForm #password").value
                })
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
    }
});