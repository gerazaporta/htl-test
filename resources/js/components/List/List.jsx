import React, { useState } from 'react';
import Table from 'react-bootstrap/Table';
import Row from './Row';

const List = ({ columns, rows, onUpdate, onDelete }) => {
    const headers = [<th key={'h_index'}>#</th>].concat(columns.map((column, index) => {
        return <th key={'h_' + index}>{column}</th>;
    })).concat([<th key={'h_action'}>Actions</th>]);
    
    const formattedRows = rows.map((data, index) => <Row key={'row_' + index} order={data} onUpdate={(id) => onUpdate(id)} onDelete={(id) => onDelete(id)} columns={columns} />)

    const body = <tbody>{formattedRows}</tbody>;

    return (
        <Table striped bordered hover>
            <thead>
                <tr>
                    {headers}
                </tr>
            </thead>
            {body}
        </Table>
    );
}

export default List;