
import React, { useEffect, useState } from 'react';
import { Link, Redirect, useHistory } from 'react-router-dom';
import { Order } from '../../clients/Order';
import List from '../../components/List/List';
import { Button } from "reactstrap";

const OrderList = () => {
    let history = useHistory();

    const [pageState, setPageState] = useState({
        isLoading: false,
        redirect: false,
    });

    const [rows, setRows] = useState([]);

    const columns = ['Vehicle', 'Technician', 'Key'];
    const getOrders = () => {
        const orderClient = new Order();
        orderClient.list()
        .then(response => response.data)
        .then(data => {
            setRows(data);
        });
    }
    useEffect(() => {
        getOrders();
    }, []);


    const login = localStorage.getItem("isLoggedIn");
    if (!login) {
        return <Redirect to="/sign-in" />;
    }
    return (
        <div>
            <List columns={columns} rows={rows} onUpdate={(id) => history.push("/orders/" + id)} onDelete={(id) => {
                const orderClient = new Order();
                orderClient.delete(id).then((data) => {
                    getOrders();
                });
            }} />
            <Button onClick={() => history.push('/home')}>Go home</Button>
            <Button onClick={() => history.push('/orders_create')}>Create</Button>
        </div>
        
    );
}

export default OrderList;