import mongoose from 'mongoose';

mongoose.Promise = global.Promise;
//my-blog
mongoose.connect('mongodb://localhost:27017/contacts-book', {
        useNewUrlParser: true,
        useUnifiedTopology: true
    }).then(() => {
        console.log('connected succesfully to DB!');
    })
    .catch(error => console.log(error));