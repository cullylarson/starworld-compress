// supports .jpg, .jpeg, .png, .svg, .gif

const path = require('path')
const {filter} = require('@cullylarson/f')
const imagemin = require('imagemin')
const ImageminMozjpeg = require('imagemin-mozjpeg')
const ImageminPngquant = require('imagemin-pngquant')
const ImageminGiflossy = require('imagemin-giflossy')
const ImageminSvgo = require('imagemin-svgo')

const argv = require('yargs')
    .usage('Usage: $0 --in=image-01.png --out=build')
    .demandOption(['in', 'out'])
    .help('h')
    .alias('h', 'help')
    .describe('out', 'Where to put minified images.')
    .argv

const processOneFile = (plugins, fileInfo) => {
    return imagemin([fileInfo.in], {
        destination: fileInfo.out,
        plugins,
    })
}

const plugins = [
    ImageminMozjpeg({quality: 70}),
    ImageminPngquant({
        quality: [0, 0.9], // the plugin will try to meet or exceed the maximum quality. if if falls below the minimum, it will throw an exception. so, set the min to zero to prevent that (we don't care if the quality must be lower, just take what we can get)
        speed: 1,
        dithering: 1,
    }),
    ImageminSvgo(),
    ImageminGiflossy({
        optimizationLevel: 3,
        lossy: 120,
        optimize: 3,
    }),
]

Promise.resolve([{in: argv.in, out: argv.out}])
    .then(filter(x => ['.jpg', '.jpeg', '.gif', '.png', '.svg', '.ico'].includes(path.extname(x.in).toLowerCase())))
    .then(xs => {
        return Promise.all(xs.map(x => processOneFile(plugins, x)))
    })
    .catch(err => {
        console.error('Something went wrong. Got error:', err)
        process.exit(1)
    })
