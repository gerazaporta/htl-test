import React, { Component, useEffect } from 'react'
import Select from 'react-select';

const CustomSelect = ({options, onSelect, name, selected }) => {

    let select = <Select options={options} onChange={onSelect} name={name} defaultValue={selected}/>;

    const getSelect = () => {
        console.log(selected);
        selected = <Select options={options} onChange={onSelect} name={name} defaultValue={selected} />;
    }

    useEffect(() => {
        getSelect();
    }, [selected]);

    return <>{select}</>;
}

export default CustomSelect;
