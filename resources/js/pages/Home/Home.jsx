import React, { Component } from "react";
import { Button } from "reactstrap";
import { Link, Redirect, useHistory } from "react-router-dom";
import { Col, Container, Row } from "react-bootstrap";


export default class Home extends Component {
    state = {
        navigate: 'home',
    };

    onLogoutHandler = () => {
        localStorage.clear();
        this.setState({
            navigate: 'home',
        });
    };

    goToOrdersList = () => {
        this.setState({
            navigate: 'orders',
        });
    };
    

    render() {
        const user = JSON.parse(localStorage.getItem("userData"));
        if (!user) {
            return <Redirect to="/sign-in" push={true} />;
        }

        const { navigate } = this.state;
        switch (navigate) {
            case 'orders':
                return <Redirect to="/orders" push={true} />;    
                break;
            default:
                break;
        }
    

        return (
                <Container fluid>
                    <Row>
                        <Col><h3> HomePage</h3></Col>
                    </Row>
                    <Row>
                        <Col><h5> Welcome, {user.first_name} </h5> You have Logged in
                        successfully.</Col>
                    </Row>
                    <Row>
                        <Col>
                        <Button onClick={this.goToOrdersList}>
                            Orders
                        </Button>
                        </Col>
                        <Col><Button
                            className="btn btn-primary text-right"
                            onClick={this.onLogoutHandler}
                        >
                            Logout
                        </Button></Col>
                    </Row>
                </Container>
        );
    }
}
