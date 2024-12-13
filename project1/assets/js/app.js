// document.addEventListener("DOMContentLoaded", () => {
//     const movieDivs = document.querySelectorAll('.movie');

//     movieDivs.forEach((movieDiv) => {
//         console.log("gelukt");
//         if(movieDiv.getAttribute('movie-id')){
//             console.log(movieDiv.getAttribute('movie-id'));
//         }

//         movieDiv.onClick = () => {
//             console.log('click');
//             //window.location.href = `detials.php?id${movieId}`;
//         }
//     });
// });

function clickOnMovie(movieId){
    window.location.href = `detials.php?id=${movieId}`;
}