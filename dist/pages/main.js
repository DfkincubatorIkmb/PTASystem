// Handle Sign In and Sign Up button toggles
const container = document.querySelector('.container');
const btnSignIn = document.querySelector('.btnSign-in');
const btnSignUp = document.querySelector('.btnSign-up');

// Add and remove 'active' class for toggling forms
btnSignIn.addEventListener('click', () => {
    container.classList.add('active');
});

btnSignUp.addEventListener('click', () => {
    container.classList.remove('active');
});

// Form submission and login validation
document.getElementById("form_input").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission

    // Predefined login credentials
    const allowedEmail = "ptaa37515@gmail.com";
    const allowedPassword = "password";

    // Get user inputs
    const emailInput = document.getElementById("email").value;
    const passwordInput = document.getElementById("password").value;

    // Validate credentials
    if (emailInput === allowedEmail && passwordInput === allowedPassword) {
        alert("Log masuk berjaya!");
        window.location.href = "../index3.html"; // Redirect to next page
    } else {
        alert("Email atau kata laluan tidak sah!");
    }
});
