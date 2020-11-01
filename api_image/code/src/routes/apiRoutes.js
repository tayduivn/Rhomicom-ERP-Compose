import {
  getContacts,
  addnewContact,
} from "../data/mongo_direct/controllers/contactsController";
const { exec, spawn } = require("child_process");

const routes = (app) => {
  let msg = "";

  app.route("/getChromePDF").post(
    (req, res, next) => {
      //console.log("Hello 1 RICHPOPO");
      //console.log(req);
      console.log(req.body);
      let cmd = req.body.browserPDFCmd;
      let cmdArgs = ` --headless --no-sandbox --disable-gpu --print-to-pdf="${req.body.pdfPath}"  ${req.body.htmPath}`;

      const ls = spawn(cmd, [cmdArgs], {shell: true});

      ls.stdout.on("data", (data) => {
        msg = msg + `stdout: ${data}<br/>`;
        console.log(msg);
      });

      ls.stderr.on("data", (data) => {
        console.log(`stderr: ${data}`);
      });

      ls.on("error", (error) => {
        console.log(`error: ${error.message}`);
      });

      ls.on("close", (code) => {
        console.log(`child process exited with code ${code}`);
        next();
      });
    },
    (req, res, next) => {
      res.end("Success!<br/>" + msg);
    }
  );

  app
    .route("/contact")
    .get(
      (req, res, next) => {
        //middleware
        msg = `Original URL:${req.originalUrl}` + `METHOD::${req.method}`;
        next();
      },
      (req, res, next) => {
        res.send("GET request successfull!<br/>" + msg);
      }
    ) //getContacts
    .post(addnewContact);

  app
    .route("/contact/:contactID")
    .get((req, res) =>
      res.send(`GET for one request successfull! ${req.params.contactID}`)
    )
    .put((req, res) =>
      res.send(`PUT for one request successfull! ${req.params.contactID}`)
    )
    .delete((req, res) =>
      res.send(`DELETE request successfull! ${req.params.contactID}`)
    );
};
export default routes;
