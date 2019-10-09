import React from 'react';
import Facebook from "./Facebook";

class Header extends React.Component {

    render() {

        return (
            <header className="App-header">
                <img src='/assets/img/logo.png' className="App-logo" alt="logo"/>
                <h1>Welcome to Smart Dating Club</h1>
                <Facebook setUserData={this.props.setUserData}/>
            </header>
        )
    }
}

export default Header;