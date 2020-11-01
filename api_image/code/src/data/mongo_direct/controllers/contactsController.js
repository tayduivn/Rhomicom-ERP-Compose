import mongoose from 'mongoose';
import {
    ContactSchema
} from '../models/contactsModel';

const Contact = mongoose.model('Contact', ContactSchema);

/*
(req, res) => {
            res.send('POST request successfull!'+JSON.stringify(req.body));
        }
         */
export const addnewContact = (req, res) => {
    let newContact = new Contact(req.body);

    newContact.save((err, contact) => {
        if (err) {
            res.send(err);
        }
        res.json(contact);
    });
}

export const getContacts = (req, res) => {
    Contact.find({}, (err, contact) => {
        if (err) {
            res.send(err);
        }
        res.json(contact);
    });
}