/**
 * @fileoverview Implements a VM for the conceptual Stack8 CPU
 * @author Florian Stolz
 * @version 0.2.2
*/

/**
 * Represents the Stack8 CPU
 */
class CPU
{
    constructor(memory,alu,stack)
    {
        this.pc = 0;
        this.memory = memory;
        this.internalALU = alu;
        this.internalStack = stack;
    }

    step()
    {
        var instruction = 0;
        var opcode = 0;

        instruction = this.memory.fetch(this.pc);
        instruction = instruction << 8;
        this.pc++;
        instruction = instruction | this.memory.fetch(this.pc);
        this.pc++;
        opcode = instruction >> 13;
        switch(opcode)
        {
            case 0:
                this.internalStack.push(this.memory.fetch((instruction & 0x1FFF)));
                break;
            case 1:
                this.memory.store((instruction & 0x1FFF),this.internalStack.pop());
                break;
            case 2:
                //console.log("Pushi");
                var pointer = 0;
                var memoryLocation = instruction & 0x1FFF;
                pointer = pointer | this.memory.fetch(memoryLocation);
                pointer = pointer << 8;
                memoryLocation++;
                pointer = pointer | this.memory.fetch(memoryLocation);
                pointer = pointer & 8191;
                //console.log("Indirect: " + pointer);
                this.internalStack.push(this.memory.fetch(pointer));
                //this.internalStack.push(this.memory.fetch(this.memory.fetch(instruction & 0x1FFF)));
                break;
            case 3:
                //console.log("Popi");
                var pointer = 0;
                var memoryLocation = instruction & 0x1FFF;
                pointer = pointer | this.memory.fetch(memoryLocation);
                pointer = pointer << 8;
                memoryLocation++;
                pointer = pointer | this.memory.fetch(memoryLocation);
                pointer = pointer & 8191;
                //console.log("Indirect: " + pointer);
                this.memory.store(pointer,this.internalStack.pop());
                //this.internalStack.push(this.memory.fetch(this.memory.fetch(instruction & 0x1FFF)));
                break;
            case 4:
                this.internalALU.performOperation(1);
                break;
            case 5:
                this.internalALU.performOperation(2);
                break;
            case 6:
                this.internalALU.performOperation(3);
                break;
            case 7:
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

    run()
    {
        ;
    }
}

/**
 * Represents the internal ALU of the CPU
 */
class ALU
{
    constructor(stackObject)
    {
        this.internalStack = stackObject; 
        this.aluInReg1 = 0;
        this.aluInReg2 = 0;
        this.aluOutReg = 0;
    }

    performAdd(inval1,inval2)
    {
        return ((inval1 + inval2)%256);
    }

    performNand(inval1,inval2)
    {
        return ~(inval1 & inval2);
    }

    performLfsh(inval1,inval2)
    {
        var tempReg = inval1;
        for(var i = 0; i < inval2; i++)
        {
            tempReg = tempReg << 1;
            tempReg = tempReg | (tempReg >> 8);
        }
        tempReg = tempReg & 0xFFFF;
        return tempReg;
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

    performOperation(opcode)
    {
        if(!(opcode > -1 && opcode < 8))
        {
            return;
        }
        this.aluInReg1 = this.internalStack.pop();
        this.aluInReg2 = this.internalStack.pop();

        if(opcode == 1)
        {
            this.aluOutReg = this.performAdd(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
        }
        if(opcode == 2)
        {
            this.aluOutReg = this.performNand(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
        }
        if(opcode == 3)
        {
            this.aluOutReg = this.performLfsh(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
        }
        if(opcode == 4)
        {
            this.aluOutReg = this.performCmp(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
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
        this.memArr = new Uint8Array(8192);
    }

    fetch(address)
    {
        return this.memArr[address];
    }

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
        this.stackObject = new Int8Array(8);
        this.currentLength = 0;
    }

    push(newEntry)
    {
        this.stackObject.copyWithin(1,0);
        this.stackObject[0] = newEntry;
        if(this.currentLength != 8)
        {
            this.currentLength++; 
        }
    }

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
