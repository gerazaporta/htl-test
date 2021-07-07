import React, { Component } from 'react'
import Home from "../pages/Home/Home";
import Signin from "../pages/Signin/Signin";

import { BrowserRouter as Router, Route, NavLink, Switch } from "react-router-dom";
import "./App.css";
import OrderList from '../pages/Order/OrderList';
import OrderCreate from '../pages/Order/OrderCreate';
import OrderUpdate from '../pages/Order/OrderUpdate';

export default class App extends Component {
    render() {
        let navLink = (
            <div className="Tab">
                <NavLink to="/sign-in" activeClassName="activeLink" className="signIn">
                    Sign In
                </NavLink>
            </div>
        );
        const login = localStorage.getItem("isLoggedIn");

        return (
            <div className="App">
                <Router>
                <Switch>
                {login ? (
<>                    
                        <Route path="/sign-in" component={Signin}></Route>
                        <Route path="/home" component={Home}></Route>
                        <Route exact path="/orders_create" component={OrderCreate}></Route>
                        <Route exact path="/orders/:id" component={OrderUpdate}></Route>
                        <Route exact path="/orders" component={OrderList}></Route>
                        </>
                ) : (
<>                    

                        {navLink}
                        <Route path="/sign-in" component={Signin}></Route>
                        <Route path="/home" component={List}></Route>
                        </>
                )}
                </Switch>
                </Router>
                
            </div>
        );
    }
}
