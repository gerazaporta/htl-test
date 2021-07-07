import React, { useEffect, useState } from 'react';
import { useHistory, useParams } from 'react-router-dom';
import { Key } from '../../clients/Key';
import { Order } from '../../clients/Order';
import { Technician } from '../../clients/Technician';
import { Vehicle } from '../../clients/Vehicle';
import CustomSelect from '../../components/Select/CustomSelect';
import { Button } from "reactstrap";


const OrderUpdate = (props) => {

    let { id } = useParams();

    const [vehicleOptions, setVehicleOptions] = useState([]);

    const [keyOptions, setKeyOptions] = useState([]);

    const [technicianOptions, setTechnicianOptions] = useState([]);

    const [order, setOrder] = useState({
        vehicle_id: '', 
        key_id: '', 
        technician_id: ''
    })
    const history = useHistory();

    const onSubmit = (event) => {
        event.preventDefault();

        const orderService = new Order();

        orderService.update(id, {
            vehicle_id: order.vehicle.value, 
            key_id: order.key.value, 
            technician_id: order.technician.value
        }).then(data => {
            return history.push('/orders');
        });

    };

    const getOrder = () => {
        const orderService = new Order();

        orderService.getOne(id)
            .then(response => response.data)
            .then(data => {
                console.log(data);
                setOrder({
                    vehicle: {
                        value: data.vehicle.id,
                        label: data.vehicle.year + ' - ' + data.vehicle.make + ' - ' + data.vehicle.model
                    }, 
                    key:  {
                        value: data.key.id,
                        label: data.key.name
                    }, 
                    technician: {
                        value: data.technician.id,
                        label: data.technician.last_name + ', ' + data.technician.first_name
                    }
                })
            });
    }

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
        if (id && id != 'create') {
            console.log(id);
            getOrder();
        }
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

export default OrderUpdate;