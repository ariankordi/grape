const express = require('express');
const colors = require('colors');
const plsbase64image = require("./tgaprocess/process");
var bodyParser = require('body-parser');
const port = 47000;
const app = express();
let TGA = require('tga');
let pako = require('pako');
let PNG = require('pngjs').PNG;
const path = require('path');
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
  extended: true
})); 

app.post('/plsbase64text', function(req, res) {
    res.send(btoa(req.get('text')));
})

app.post('/plsbase64memo', function(req, res) {
    let data = req.body.image;
    //data = data.replace(/\0/g, "").replace(/\r?\n|\r/g, "").trim();
    let paintingBuffer = Buffer.from(data, 'base64');
        let output = '';
        try
        {
            output = pako.inflate(paintingBuffer);
        }
        catch (err)
        {
            console.error(err);
        }
        let tga = new TGA(Buffer.from(output));
        let png = new PNG({
            width: tga.width,
            height: tga.height
        });
        png.data = tga.pixels;
        let pngBuffer = PNG.sync.write(png);
        return res.send(`data:image/png;base64,${pngBuffer.toString('base64')}`);
});

app.listen(port, () => {
    console.log(`[INFO] Server started on port ${port}.`.green)
});
