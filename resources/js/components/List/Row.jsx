import React from 'react';
import { Button } from 'reactstrap';

const Row = (props) => {
    return (           
        <tr>
            <td>{props.order.id}</td>
            <td>{props.order.vehicle.year + ' - ' + props.order.vehicle.make + ' - ' + props.order.vehicle.model}</td>
            <td>{props.order.technician.last_name + ', ' + props.order.technician.first_name}</td>
            <td>{props.order.key.name}</td>
            <td><Button onClick={() => props.onUpdate(props.order.id)}>Update</Button><Button variant="danger" onClick={() => props.onDelete(props.order.id)}>Delete</Button></td>
        </tr>
    );
}

export default Row;