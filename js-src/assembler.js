/**
 * @fileoverview Implements an basic assembler for the Stack8 CPU
 * @author Florian Stolz
 * @version 0.1.0
*/

/**
 * Implements the assembler functionialities
 */
class Assembler
{
    constructor()
    {

    }

    assemble(input,memory)
    {
        var labels = new Array();
        var inputlines = input.split('\n');
        var address = 0;
        for(var i = 0; i < inputlines.length; i++)
        {
            if(inputlines[i].indexOf(":") >= 0)
            {
                labels[inputlines[i].toLowerCase().split(':')[0]] = address;
            }
            else
            {
            address += 2;
            }
        }

        /*
        for(var key in labels)
        {
            console.log("KEY: "+ key + " ADDRESS: "+labels[key]);
        }
        */

        var instruction;
        address = 0;
        var linesplit;
        var argumentFlag = 0;
        var dataFlag = 0;

        for(var i = 0; i < inputlines.length; i++)
        {
            instruction = 0;
            argumentFlag = 0;
            dataFlag = 0;

            if(inputlines[i].indexOf(":") >= 0 || inputlines[i] === "")
            {
                continue;
            }

            linesplit = inputlines[i].toLowerCase().split(' ');
            switch(linesplit[0])
            {
                case "push":
                    //console.log("Caught Push!");
                    instruction = instruction | 0;
                    argumentFlag = 1;
                    break;
                case "pop":
                    //console.log("Caught Pop!");
                    instruction = instruction | 1;
                    argumentFlag = 1;
                    break;
                case "db":
                    //console.log("Caught DB!");
                    instruction = instruction | 0;
                    dataFlag = 1;
                    argumentFlag = 1;
                    break;
                case "add":
                    //console.log("Caught Add!");
                    instruction = instruction | 4;
                    instruction = instruction << 13;
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
                case "lshift":
                    //console.log("Caught LeftShift!");
                    instruction = instruction | 6;
                    instruction = instruction << 13;
                    dataFlag = 0;
                    argumentFlag = 0;
                    break;
                case "jmple":
                    //console.log("Caught JUMPLE!");
                    instruction = instruction | 7;
                    dataFlag = 0;
                    argumentFlag = 1;
                    break;
                default:
                    console.log("Unknown instruction. Stopping...");
                    return;
            }

            if(argumentFlag > 0)
            {
                if(linesplit[1] == "undefined")
                {
                    console.log("No valid argument found. Stopping...");
                }

                if(dataFlag == 0)
                {
                    if(labels[linesplit[1].toLowerCase()] == "undefined")
                    {
                        console.log("No valid label found. Stopping...");
                    }
                    instruction = instruction << 13;
                    instruction = instruction | labels[linesplit[1].toLowerCase()];
                }
                else
                {
                    instruction = instruction | parseInt(linesplit[1],10);
                    instruction = instruction << 8;
                }
            }

            memory.store(address,instruction >> 8);
            address++;
            memory.store(address,instruction & 0xFF);
            address++;
        }
    }

    disassemble()
    {
        ;
    }
}