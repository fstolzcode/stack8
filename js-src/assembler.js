/**
 * @fileoverview Implements an basic assembler for the Stack8 CPU
 * @author Florian Stolz
 * @version 0.1.5
*/

/**
 * Implements the assembler functionialities
 */
class Assembler
{
    constructor()
    {

    }

    //assemble input and store in memory
    assemble(input,memory)
    {
        var labels = new Array(); //Label resolve
        var inputlines = input.split('\n'); //split lines
        var address = 0;
        for(var i = 0; i < inputlines.length; i++)
        {
            if(inputlines[i].indexOf(":") >= 0) //got a label, store the address
            {
                labels[inputlines[i].toLowerCase().split(':')[0]] = address;
            }
            else
            {
            address += 2; //each instruction is two bytes long... incress address
            }
        }

        /*
        for(var key in labels)
        {
            console.log("KEY: "+ key + " ADDRESS: "+labels[key]);
        }
        */

        //go through input again
        address = 0;
        var instruction;
        var linesplit;
        var argumentFlag = 0;
        var dataFlag = 0;
        var pointerFlag = 0;

        for(var i = 0; i < inputlines.length; i++)
        {
            instruction = 0;
            argumentFlag = 0;
            dataFlag = 0;
            pointerFlag = 0;

            if(inputlines[i].indexOf(":") >= 0 || inputlines[i] === "") //Label, ignore
            {
                continue;
            }

            //parse the line, only allow words, statements starting with # and +, everything else ignored (comments)
            linesplit = inputlines[i].toLowerCase().match(/(\b(\w+)(\+[0-9]+)?\b|#\d+)/g); //\b(\w+)\b //(\b(\w+)\b|#\d+)
            switch(linesplit[0])
            {
                case "push":
                    //console.log("Caught Push!");
                    instruction = instruction | 0; //set the opcodes
                    argumentFlag = 1; //do we need an argument
                    break;
                case "pop":
                    //console.log("Caught Pop!");
                    instruction = instruction | 1;
                    argumentFlag = 1;
                    break;
                case "pushi":
                    instruction = instruction | 2;
                    argumentFlag = 1;
                    break;
                case "popi":
                    instruction = instruction | 3;
                    argumentFlag = 1;
                    break;
                case "dp":
                    dataFlag = 1;   //it is data
                    argumentFlag = 1;
                    pointerFlag = 1;
                    break;
                case "db":
                    //console.log("Caught DB!");
                    //instruction = instruction | 0;
                    dataFlag = 1;
                    argumentFlag = 1;
                    break;
                case "add":
                    //console.log("Caught Add!");
                    instruction = instruction | 4;
                    instruction = instruction << 13;    //everything after opcode is ignored by VM... just zero it
                    dataFlag = 0;
                    argumentFlag = 0;
                    break;
                case "nand":
                    //console.log("Caught Nand!");
                    instruction = instruction | 5;
                    instruction = instruction << 13;
                    dataFlag = 0;
                    argumentFlag = 0;
                    break;
                case "jmpule":
                    //console.log("Caught LeftShift!");
                    instruction = instruction | 6;
                    //instruction = instruction << 13;
                    dataFlag = 0;
                    argumentFlag = 1;
                    break;
                case "jmple":
                    //console.log("Caught JUMPLE!");
                    instruction = instruction | 7;
                    dataFlag = 0;
                    argumentFlag = 1;
                    break;
                default:
                    //console.log("Unknown instruction. Stopping...");
                    return "Line "+(i+1)+": Unknown instruction \""+linesplit[0]+"\"";
                    //return;
            }

            //we need arguments
            if(argumentFlag > 0)
            {   
                //none found
                if(!linesplit[1])
                {
                    //console.log("No valid argument found. Stopping...");
                    return "Line "+(i+1)+": No valid argument found";
                    //return;
                }

                //we don't need data
                if(dataFlag == 0)
                {   
                    //check if we got a label with an offset
                    if(linesplit[1].indexOf("+") > 0)
                    {   
                        //split at the offset
                        var baseLabel = linesplit[1].split("+")[0];
                        //console.log("BaseLabel: "+baseLabel);
                        var baseOffset = linesplit[1].split("+")[1];
                        //console.log("Offset: "+baseOffset);
                        if(labels[baseLabel.toLowerCase()] != null) //Check for an label
                        {
                            //console.log(labels[baseLabel.toLowerCase()]);
                            //console.log((labels[baseLabel.toLowerCase()] + baseOffset));
                            instruction = instruction << 13; //make room for address + offset
                            instruction = instruction | ( ( parseInt(labels[baseLabel.toLowerCase()]) + parseInt(baseOffset)) % 8191);
                        }
                        else
                        {
                            return "Line "+(i+1)+": No valid label found"; 
                        }
                    }
                    else if(labels[linesplit[1].toLowerCase()] == null) //Check for label... none?
                    {
                        
                        if(linesplit[1].indexOf("#") == 0) //Maybe an absolute address?
                        {
                            var argumentAddress = linesplit[1].substring(1).replace(/[ \t]/g, '').match(/^\d+$/g); //only parse numbers
                            if(!argumentAddress)
                            {
                                //console.log("No valid argument found. Stopping...");
                                return "Line "+(i+1)+": No valid argument found";
                                //return;
                            }
                            instruction = instruction << 13; //store address
                            instruction = instruction | (parseInt(argumentAddress) % 8192);
                        }
                        else
                        {
                            //console.log("No valid label/address found. Stopping...");
                            return "Line "+(i+1)+": No valid label/address found";
                            //return;
                        }

                        //console.log("No valid label found. Stopping...");
                        //return;
                    }
                    else
                    {
                        instruction = instruction << 13; //no extras, just fill in label address
                        instruction = instruction | parseInt(labels[linesplit[1].toLowerCase()]);
                    }
                }
                else //data
                {
                    //get the arguments, split at "," for multiple
                    var argumentIndex = inputlines[i].indexOf(linesplit[1]);
                    var dataString = inputlines[i].substring(argumentIndex);
                    var argumentSplit = dataString.split(",");
                    var argument = 0;
                    if(pointerFlag == 0) //no pointer
                    {
                        for(var j = 0; j < 2; j++) //artificial limitation, to make label resolve not so complicated for now
                        {
                            if(!argumentSplit[j])
                            {
                                memory.store(address,0);
                                address++;
                                continue;
                            }
                            if( (argument = argumentSplit[j].replace(/[ \t]/g, '').match(/^\d+$/g)) ) //only parse numbers
                            {
                                memory.store(address,argument);
                                address++;
                            }
                            else
                            {
                                //console.log("Invalid argument. Stopping");
                                return "Line "+(i+1)+": Invalid argument";
                                //return;
                            }
                        }
                    }
                    else
                    {
                        if(!argumentSplit[0]) //get the pointer, nothing provided... just zero
                        {
                            memory.store(address,0);
                            address++;
                            memory.store(address,0);
                            address++;
                        }
                        if( (argument = argumentSplit[0].replace(/[ \t]/g, '').match(/^\d+$/g)) ) //only parse numbers
                        {
                            argument = argument & 8191;
                            memory.store(address,(argument >> 8));
                            address++;
                            memory.store(address,(argument & 0xFF));
                            address++;
                        }
                    }
                    /*
                    if((argumentSplit.length % 2) != 0)
                    {
                        memory.store(address,0);
                        address++;
                    }
                    */
                    //instruction = instruction | parseInt(linesplit[1],10);
                    //instruction = instruction << 8;
                }
            }

            //No data, then store instruction
            if(dataFlag == 0)
            { 
                memory.store(address,( ( (instruction >> 8) & 0xFF) ) );
                address++;
                memory.store(address, (instruction & 0xFF));
                address++;
            }
        }
        return "Success";
    }
}