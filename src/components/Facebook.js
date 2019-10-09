import React from 'react';

export default class Facebook extends React.Component {


    state = {
        loading: true,
        person: null
    };





    async statusChangeCallback(response) {
        console.log(response);
        if (response.status === 'connected') {
            const url = "https://smart-dating.club/api/?hello";
            const reply = await fetch(url, {
                credentials: 'include'
            });
            this.setState({person: reply.json()})
        } else if (response.status === 'not_authorized') {
            console.log("Please log into this app.");
        } else {
            console.log("Please log into this facebook.");
        }
    }

    checkLoginState = () => {
        window.FB.getLoginStatus(function (response) {
            this.statusChangeCallback(response);
        }.bind(this));
    }

    handleFBLogin = () => {
        window.FB.login(this.checkLoginState());
    }

    render() {
        return (
            <div>
                <button onClick={this.handleFBLogin}>Login</button>
                <div className="fb-login-button" data-width="500" data-size="large" data-button-type="continue_with"
                     data-auto-logout-link="true" data-use-continue-as="true"></div>
            </div>
        );
    }
}
