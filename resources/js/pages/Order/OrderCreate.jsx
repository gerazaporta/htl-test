import React, { useEffect, useState } from 'react';
import { useHistory, useParams } from 'react-router-dom';
import { Key } from '../../clients/Key';
import { Order } from '../../clients/Order';
import { Technician } from '../../clients/Technician';
import { Vehicle } from '../../clients/Vehicle';
import CustomSelect from '../../components/Select/CustomSelect';
import { Button } from "reactstrap";


const OrderCreate = (props) => {
    const history = useHistory();

    const [vehicleOptions, setVehicleOptions] = useState([]);

    const [keyOptions, setKeyOptions] = useState([]);

    const [technicianOptions, setTechnicianOptions] = useState([]);

    const [order, setOrder] = useState({
        vehicle: '', 
        key: '', 
        technician: ''
    })

    const onSubmit = (event) => {
        const orderService = new Order();

        orderService.create({
            vehicle_id: order.vehicle.value, 
            key_id: order.key.value, 
            technician_id: order.technician.value
        }).then(data => {
            return history.push('/orders');
        });

        event.preventDefault();

    };

    const pullKeySelectOptions = () => {
        const keyService = new Key();

            keyService.list()
            .then(response => response.data)
            .then(data => {
                return data.map(elem => {
                    return {
                        value: elem.id,
                        label: elem.name
                    }
                })
            })
            .then(data => {
                console.log(data);
                setKeyOptions(data);
            });
    }

    const pullVehicleSelectOptions = () => {
        const vehicleService = new Vehicle();

            vehicleService.list()
            .then(response => response.data)
            .then(data => {
                return data.map(elem => {
                    return {
                        value: elem.id,
                        label: elem.year + ' - ' + elem.make + ' - ' + elem.model
                    }
                })
            })
            .then(data => {
                console.log(data);
                setVehicleOptions(data);
            });
    }

    const pullTechnicianSelectOptions = () => {
        const technicianService = new Technician();

            technicianService.list()
            .then(response => response.data)
            .then(data => {
                return data.map(elem => {
                    return {
                        value: elem.id,
                        label: elem.last_name + ', ' + elem.first_name
                    }
                })
            })
            .then(data => {
                console.log(data);
                setTechnicianOptions(data);
            });

    }

    useEffect(() => {
        pullKeySelectOptions();
        pullVehicleSelectOptions();
        pullTechnicianSelectOptions();
    }, []);

    const selectStyle = {
        width: '99%'
    };

    const user = JSON.parse(localStorage.getItem("userData"));
    if (!user) {
        return <Redirect to="/sign-in" push={true} />;
    }
  
    

    return <div><form onSubmit={onSubmit}>
        <label style={selectStyle}>
            Technician:
            <CustomSelect selected={order.technician} options={technicianOptions} onSelect={(technician) => {
                setOrder({...order, technician: technician})
            }} />
        </label>
        <label style={selectStyle}>
            Vehicle:
            <CustomSelect selected={order.vehicle} options={vehicleOptions} onSelect={(vehicle) => {
                setOrder({...order, vehicle: vehicle})
            }}/>
        </label>
        <label style={selectStyle}>
            Key:
            <CustomSelect selected={order.key} options={keyOptions} onSelect={(key) => {
                setOrder({...order, key: key})
            }} />
        </label>
    <input type="submit" value="Submit" />
  </form>
  <Button onClick={() => history.push('/orders')}>Go to Orders List</Button>
  </div>;

    
}

export default OrderCreate;