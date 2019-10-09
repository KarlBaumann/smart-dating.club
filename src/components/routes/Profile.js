import React, {Component} from 'react';

class Profile extends Component {

    componentDidMount() {
        const url = "/"
    }

    render() {
        console.log('props', this.props)
        console.log('state', this.state)
        console.log('facebookData', this.props.facebookData)
        return (
            <div>
                <p>
                    Profile
                </p>
            </div>
        );
    }
}

export default Profile;