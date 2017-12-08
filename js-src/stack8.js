/**
 * @fileoverview Implements a VM for the conceptual Stack8 CPU
 * @author Florian Stolz
 * @version 0.2.3
*/

/**
 * Represents the Stack8 CPU
 */
class CPU
{
    constructor(memory,alu,stack) //Internals
    {
        this.pc = 0;
        this.memory = memory;
        this.internalALU = alu;
        this.internalStack = stack;
    }

    step()
    {
        var instruction = 0; //instruction data
        var opcode = 0; //opcode

        instruction = this.memory.fetch(this.pc); //get first part of instruction
        instruction = instruction << 8;
        this.pc++;
        instruction = instruction | this.memory.fetch(this.pc); //get second part of instruction
        this.pc++;
        opcode = instruction >> 13; //get the opcode
        switch(opcode)
        {
            case 0: //push
                this.internalStack.push(this.memory.fetch((instruction & 0x1FFF)));
                break;
            case 1: //pop
                this.memory.store((instruction & 0x1FFF),this.internalStack.pop());
                break;
            case 2: //pushi
                //console.log("Pushi");
                var pointer = 0;
                var memoryLocation = instruction & 0x1FFF; //get the memory location
                pointer = pointer | this.memory.fetch(memoryLocation); //set the pointer
                pointer = pointer << 8;
                memoryLocation++;
                pointer = pointer | this.memory.fetch(memoryLocation);
                pointer = pointer & 8191;
                //console.log("Indirect: " + pointer);
                this.internalStack.push(this.memory.fetch(pointer));
                //this.internalStack.push(this.memory.fetch(this.memory.fetch(instruction & 0x1FFF)));
                break;
            case 3: //popi
                //console.log("Popi");
                var pointer = 0;
                var memoryLocation = instruction & 0x1FFF; //get the memory location
                pointer = pointer | this.memory.fetch(memoryLocation); //set the pointer
                pointer = pointer << 8;
                memoryLocation++;
                pointer = pointer | this.memory.fetch(memoryLocation);
                pointer = pointer & 8191;
                //console.log("Indirect: " + pointer);
                this.memory.store(pointer,this.internalStack.pop());
                //this.internalStack.push(this.memory.fetch(this.memory.fetch(instruction & 0x1FFF)));
                break;
            case 4: //add
                this.internalALU.performOperation(1);
                break;
            case 5: //nand
                this.internalALU.performOperation(2);
                break;
            case 6: //jmpule
                this.internalALU.performOperation(3);
                if(this.internalStack.pop() == 1)
                {
                    this.pc = (instruction & 0x1FFF);
                }
                break;
            case 7: //jmple
                this.internalALU.performOperation(4);
                if(this.internalStack.pop() == 1)
                {
                    this.pc = (instruction & 0x1FFF);
                }
                break;
            default:
                console.log("Unknown instruction "+ opcode +". Stopping...");
                return;
        }
    }
}

/**
 * Represents the internal ALU of the CPU
 */
class ALU
{
    constructor(stackObject)
    {
        this.internalStack = stackObject;   //Stack of the machine
        this.aluInReg1 = 0; //internal registers
        this.aluInReg2 = 0;
        this.aluOutReg = 0;
    }

    //Basic arithmetic functions and jump
    performAdd(inval1,inval2)
    {
        return ((inval1 + inval2)%256);
    }

    performNand(inval1,inval2)
    {
        return ~(inval1 & inval2);
    }

    performUCmp(inval1, inval2)
    {
        var uInval1 = (new Uint32Array([inval1]))[0];
        var uInval2 = (new Uint32Array([inval2]))[0];
        if(uInval1 <= uInval2)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    performCmp(inval1, inval2)
    {
        if(inval1 <= inval2)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    //Wrapper for functions
    performOperation(opcode)
    {
        if(!(opcode > -1 && opcode < 8))
        {
            return;
        }
        this.aluInReg1 = this.internalStack.pop(); //Get the operands from the stack
        this.aluInReg2 = this.internalStack.pop();

        if(opcode == 1)
        {
            this.aluOutReg = this.performAdd(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg); //push the result
        }
        if(opcode == 2)
        {
            this.aluOutReg = this.performNand(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);//push the result
        }
        if(opcode == 3)
        {
            /*
            this.aluOutReg = this.performLfsh(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
            */
            this.aluOutReg = this.performUCmp(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);//push the result
        }
        if(opcode == 4)
        {
            this.aluOutReg = this.performCmp(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);//push the result
        }
    }
}

/**
 * Represents the external memory with a size of 8 Kib
 */
class Memory
{
    constructor()
    {
        this.memArr = new Uint8Array(8192); //Internal memory
    }

    //get byte from memory
    fetch(address)
    {
        return this.memArr[address];
    }

    //set byte at address
    store(address, value)
    {
        this.memArr[address] = value;
    }
}

/**
 * Represents an internal Stack with a depth of 8
 */
class Stack
{
    constructor()
    {
        this.stackObject = new Int8Array(8); //internal array as stack
        this.currentLength = 0;
    }

    //push an entry to the stack by copying and moving the array
    push(newEntry)
    {
        this.stackObject.copyWithin(1,0);
        this.stackObject[0] = newEntry;
        if(this.currentLength != 8)
        {
            this.currentLength++; 
        }
    }

    //pop an entry, by getting first entry and then moving the array one element to the left, also zero out everything
    pop()
    {
        if(this.currentLength == 0) return 0;

        var poppedValue = this.stackObject[0];

        this.stackObject.copyWithin(0,1);

        this.stackObject[this.currentLength - 1] = 0;
        this.currentLength--;

        return poppedValue;
    }

}
