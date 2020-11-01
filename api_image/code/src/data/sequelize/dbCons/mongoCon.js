import mongoose from 'mongoose';
mongoose.Promise = global.Promise;
mongoose.connect('mongodb://localhost/my-blog', {
    useNewUrlParser: true,
    useUnifiedTopology: true
})