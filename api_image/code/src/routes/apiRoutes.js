const { exec, spawn } = require("child_process");

const routes = (app) => {
  let msg = "";
  //API to convert HTML Report to PDF
  app.route("/getChromePDF").post(
    (req, res, next) => {
      //console.log(req.body);
      let cmd = req.body.browserPDFCmd;
      let cmdArgs = ` --headless --no-sandbox --disable-gpu --print-to-pdf="${req.body.pdfPath}"  ${req.body.htmPath}`;

      const ls = spawn(cmd, [cmdArgs], { shell: true });

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
      res.end("Success!");
    }
  );
  //API to start Java Process Runner for a Report Run
  app.route("/startJavaRunner").post(
    (req, res, next) => {
      //console.log("Hello 1 RICHPOPO");
      //console.log(req);
      //console.log(req.body);"java -jar " . $rnnrPrcsFile . " " . $strArgs;
      let cmd = "java";
      let cmdArgs = ` -jar "${req.body.rnnrPrcsFile}"  ${req.body.strArgs}`;

      const ls = spawn(cmd, [cmdArgs], { shell: true });

      ls.stdout.on("data", (data) => {
        msg = msg + `stdout: ${data}<br/>`;
        console.log(msg);
        next();
      });

      ls.stderr.on("data", (data) => {
        console.log(`stderr: ${data}`);
        next();
      });

      ls.on("error", (error) => {
        console.log(`error: ${error.message}`);
        next();
      });

      ls.on("close", (code) => {
        console.log(`child process exited with code ${code}`);
        next();
      });
    },
    (req, res, next) => {
      res.end("Success!");
    }
  );
};
export default routes;
