const express = require('express');
const router = express.Router();

router.post('/plsbase64memo', function(req, res) {
    let data = req.body.image;
    data = data.replace(/\0/g, "").replace(/\r?\n|\r/g, "").trim();
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