const loginButton = document.querySelector('.login');
const signupButton = document.querySelector('.signup-prompt');

loginButton.addEventListener('click', ()=> {
    open("login.php", "_self");
});
signupButton.addEventListener('click', ()=> {
    open("signup.php", "_self");
});
