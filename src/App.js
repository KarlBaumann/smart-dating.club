import './App.css';

import React, {Component} from "react";
import {
    BrowserRouter as Router,
    Route,
    Link,
    Redirect,
    withRouter
} from "react-router-dom";


import Header from './components/Header';
import Home from './components/routes/Home';
import Profile from './components/routes/Profile';


class App extends Component {

    state = {
        isLoggedIn: false,
        userID: '',
        name: '',
        email: '',
        picture: ''
    };

    setUserData = (userData) => {
        this.setState(userData)
    }


    render() {

        let facebookData = {};

        return (
            <div className="App">
                <Header setUserData={this.setUserData}/>
                <Router>
                    <Route path="/" component={Home} exact/>
                    <Route path="/profile" component={Profile}/>
                </Router>
            </div>
        );
    }
}

export default App;
