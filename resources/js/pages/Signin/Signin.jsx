import React, { Component } from "react";
import { Button, Form, FormGroup, Label, Input } from "reactstrap";
import { Redirect } from "react-router-dom";
import {Auth} from "../../clients/Auth";

export default class Signin extends Component {
    constructor(props) {
        super(props);
        this.state = {
            email: "",
            password: "",
            msg: "",
            isLoading: false,
            redirect: false,
            errMsgEmail: "",
            errMsgPwd: "",
            errMsg: "",
        };
    }
    onChangehandler = (e) => {
        let name = e.target.name;
        let value = e.target.value;
        let data = {};
        data[name] = value;
        this.setState(data);
    };

    onSignInHandler = () => {
        this.setState({ isLoading: true });
        const authClient = new Auth();
            authClient.login({
                email: this.state.email,
                password: this.state.password,
            }).then((response) => {
                    this.setState({ isLoading: false });
                    if (response !== undefined && response.status === 200) {
                        localStorage.setItem("isLoggedIn", true);
                        localStorage.setItem("userData", JSON.stringify(response));
                        this.setState({
                            msg: 'Welcome',
                            redirect: true,
                        });
                    }
                    if (response !== undefined) {
                        this.setState({
                            errMsgEmail: response.errors.email[0],
                            errMsgPwd: response.errors.password[0],
                        });
                        setTimeout(() => {
                            this.setState({ errMsgEmail: "", errMsgPwd: "" });
                        }, 2000);
                    }
                }).catch(err => {
                console.log('ERR');
                // this.setState({
                //     errMsgEmail: err.errors.email[0],
                //     errMsgPwd: err.errors.password[0],
                // });
            });
    };

    render() {
        if (this.state.redirect) {
            return <Redirect to="/home" />;
        }
        const login = localStorage.getItem("isLoggedIn");
        if (login) {
            return <Redirect to="/home" />;
        }
        const isLoading = this.state.isLoading;
        return (
            <div>
                <Form className="containers">
                    <FormGroup>
                        <Label for="email">Email id</Label>
                        <Input
                            type="email"
                            name="email"
                            placeholder="Enter email"
                            value={this.state.email}
                            onChange={this.onChangehandler}
                        />
                        <span className="text-danger">{this.state.msg}</span>
                        <span className="text-danger">{this.state.errMsgEmail}</span>
                    </FormGroup>
                    <FormGroup>
                        <Label for="password">Password</Label>
                        <Input
                            type="password"
                            name="password"
                            placeholder="Enter password"
                            value={this.state.password}
                            onChange={this.onChangehandler}
                        />
                        <span className="text-danger">{this.state.errMsgPwd}</span>
                    </FormGroup>
                    <p className="text-danger">{this.state.errMsg}</p>
                    <Button
                        className="text-center mb-4"
                        color="success"
                        onClick={this.onSignInHandler}
                    >
                        Sign In
                        {isLoading ? (
                            <span
                                className="spinner-border spinner-border-sm ml-5"
                                role="status"
                                aria-hidden="true"
                            ></span>
                        ) : (
                            <span></span>
                        )}
                    </Button>
                </Form>
            </div>
        );
    }
}
